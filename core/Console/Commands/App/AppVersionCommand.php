<?php

namespace Core\Console\Commands\App;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class AppVersionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display the current framework version';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Filesystem $files)
    {
        $logo = $files->get(stubs_path('app/name.stub'));
        $logo = $this->buildReplacements($logo);
        $this->line($logo);
    }

    /**
     * Build the replacements strings.
     *
     * @param  string $name
     * @return string
     */
    protected function buildReplacements($name)
    {
        return str_replace(
            ['DummyName', 'DummyVersion'],
            [config('app.name'), $this->laravel->version()],
            $name
        );
    }
}
