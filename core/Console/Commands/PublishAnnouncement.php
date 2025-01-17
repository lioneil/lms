<?php

namespace Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PublishAnnouncement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcement:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish announcement periodically';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now=date("Y-m-d");
        $data=DB::table('announcement')->whereRaw("published_at<$now")->get();
    }
}
