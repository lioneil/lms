<?php

namespace Course\Console\Commands;

use Course\Services\CourseServiceInterface;
use Illuminate\Console\Command;

class CourseCleanCommand extends Command
{
    const EXPIRED_KEY = 'expired';
    const DELETE_ALL_KEY = 'all';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:clean
                           {--expired=true : Soft delete expired courses}
                           {--all : Permanently delete all courses}
                           {--yes-all-did-i-stutter : Really delete all courses}
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired courses from database';

    /**
     * Execute the console command.
     *
     * @param  \Course\Services\CourseServiceInterface $service
     * @return void
     */
    public function handle(CourseServiceInterface $service)
    {
        $type = $this->qualifyType();

        if ($type == self::EXPIRED_KEY) {
            $service->autoclean();
        } elseif ($type == self::DELETE_ALL_KEY) {
            $this->call('db:truncate', [
                'name' => 'courses,lessons,lessonstree'
            ]);
        }
    }

    /**
     * Determine the type of cleaning.
     *
     * @return string
     */
    protected function qualifyType()
    {
        $type = $this->option('expired') == true ? self::EXPIRED_KEY : null;
        $all = $this->option('all') && $this->option('yes-all-did-i-stutter');
        $question = 'You are about to permanently delete all courses. Are you sure you want to proceed?';

        if ($all && $this->ask($question, 'no') == 'yes') {
            $type = self::DELETE_ALL_KEY;
        }

        return $type;
    }
}
