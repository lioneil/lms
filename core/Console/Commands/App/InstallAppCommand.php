<?php

namespace Core\Console\Commands\App;

use Core\Console\Commands\InteractsWithEnvironmentFile;
use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;

class InstallAppCommand extends Command
{
    use ConfirmableTrait, InteractsWithEnvironmentFile;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactively perform application setup';

    /**
     * The Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Array of details.
     *
     * @var string[]
     */
    protected $details;

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->clearOptimizations();
        $this->callApplicationBanner();
        $this->promptWelcomeMessage();
        $this->copyEnvironmentFile();
        $this->generateApplicationKey();
        $this->defineAppDetails();
        $this->defineDatabaseDetails();
        $this->defineMailDetails();
        $this->reloadEnvironment();
        $this->runMigrations();
        $this->runOptimizations();
    }

    /**
     * Clear the bootstra/cache folder
     * to ensure a smooth installation.
     *
     * @return void
     */
    protected function clearOptimizations(): void
    {
        $this->callSilent('optimize:clear');
    }

    /**
     * Display the application version.
     *
     * @return void
     */
    protected function callApplicationBanner(): void
    {
        $this->call('app:version');
        sleep(1);
    }

    /**
     * Display the welcome message.
     *
     * @return void
     */
    protected function promptWelcomeMessage(): void
    {
        $this->line($this->files->get(
            base_path('docs/Installation Guide/00 - Before We Begin.md')
        ));

        if (! $this->confirmToProceed()) {
            return;
        }
    }

    /**
     * Copy the .env.sample to .env
     *
     * @return void
     */
    protected function copyEnvironmentFile(): void
    {
        $this->warn(' - Copying .env.example to .env');

        if (! file_exists(base_path('.env'))) {
            copy(base_path('.env.example'), base_path('.env'));
            $this->info(' - Copied.');
        } else {
            $this->info(' - File already exists. Skipping.');
        }

        $this->line(PHP_EOL);
    }

    /**
     * Generate the application encryption key.
     *
     * @return void
     */
    protected function generateApplicationKey(): void
    {
        $this->callSilent('key:generate', ['--ansi' => true]);
    }

    /**
     * Define the application title, tagline, etc.
     *
     * @return void
     */
    protected function defineAppDetails(): void
    {
        $this->line(trans('console.specify_app_details'));

        $defaults = $this->laravel['config']['app'];

        $this->writeEnvironmentFileFromArray([
            'APP_NAME' => $this->ask(__('Application Title'), $defaults['name']),
            'APP_TAGLINE' => $this->ask(__('Application Tagline'), env('APP_TAGLINE')),
            'APP_AUTHOR' => $this->ask(__('Application Author'), env('APP_AUTHOR')),
            'APP_YEAR' => $this->ask(__('Application Year'), env('APP_YEAR')),
            'APP_ENV' => $this->ask(__('Application Environment'), $defaults['env']),
            'APP_DEBUG' => $this->ask(__('Application Debug'), $defaults['debug']),
        ]);
    }

    /**
     * Define the database url, host, username, and password.
     *
     * @return void
     */
    protected function defineDatabaseDetails(): void
    {
        $this->line(trans('console.define_database_details'));

        $this->writeEnvironmentFileFromArray([
            'DB_CONNECTION' => $this->details['DB']['driver'] = $this->ask(__('Database Driver'), env('DB_CONNECTION')),
            'DB_HOST' => $this->details['DB']['host'] = $this->ask(__('Database Host'), env('DB_HOST')),
            'DB_PORT' => $this->details['DB']['port'] = $this->ask(__('Database Port'), env('DB_PORT')),
            'DB_DATABASE' => $this->details['DB']['database'] = $this->ask(__('Database Name'), env('DB_DATABASE')),
            'DB_USERNAME' => $this->details['DB']['username'] = $this->ask(__('Database Username'), env('DB_USERNAME')),
            'DB_PASSWORD' => $this->details['DB']['password'] = $this->ask(__('Database Password'), env('DB_PASSWORD')),
        ]);
    }

    /**
     * Define the mail credentials to be used.
     *
     * @return void
     */
    protected function defineMailDetails(): void
    {
        if (! $this->confirm(trans('console.confirm_continue_mail_details'), false)) {
            return;
        }

        $this->line(trans('console.define_mail_credentials'));

        $this->writeEnvironmentFileFromArray([
            'MAIL_DRIVER' => $this->ask(__('Mail Driver'), env('MAIL_DRIVER')),
            'MAIL_ENCRYPTION' => $this->ask(__('Mail Encryption'), env('MAIL_ENCRYPTION')),
            'MAIL_FROM_ADDRESS' => $this->ask(__('Mail Address'), env('MAIL_FROM_ADDRESS')),
            'MAIL_FROM_NAME' => $this->ask(__('Mail From Name'), env('MAIL_FROM_NAME')),
            'MAIL_HOST' => $this->ask(__('Mail Host'), env('MAIL_HOST')),
            'MAIL_PASSWORD' => $this->ask(__('Mail Password'), env('MAIL_PASSWORD')),
            'MAIL_PORT' => $this->ask(__('Mail Port'), env('MAIL_PORT')),
            'MAIL_PRETEND' => $this->ask(__('Mail Pretend'), env('MAIL_PRETEND')),
            'MAIL_USERNAME' => $this->ask(__('Mail Username'), env('MAIL_USERNAME')),
        ]);
    }

    /**
     * Reload the env file.
     *
     * @return void
     */
    protected function reloadEnvironment(): void
    {
        try {
            config(['database.connections.console' => array_merge(
                config('database.connections.'.$this->details['DB']['driver']),
                collect($this->details['DB'])->except('driver')->toArray()
            )]);
        } catch (\Exception $e) {
            unset($e);
        }
    }

    /**
     * Run the console command to migrate
     * to database.
     *
     * @return void
     */
    protected function runMigrations(): void
    {
        $this->line(trans('console.run_migrations'));

        $this->call('migrate', [
            '--seed' => true,
            '--database' => 'console',
        ]);
    }

    /**
     * Run the optimize command.
     *
     * @return void
     */
    public function runOptimizations(): void
    {
        if (! $this->confirm(__('Do you wish to run the optimize command?'), 1)) {
            return;
        }

        $this->call('optimize');
    }
}
