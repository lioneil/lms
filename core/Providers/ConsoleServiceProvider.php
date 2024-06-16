<?php

namespace Core\Providers;

use Core\Console\Commands\App\AppNameCommand;
use Core\Console\Commands\App\ServeCommand;
use Core\Console\Commands\Make\ModelMakeCommand;
use Core\Console\Commands\Make\TestMakeCommand;
use Core\Console\Commands\Migrations\MigrateCommand;
use Core\Console\Commands\Migrations\MigrateMakeCommand;
use Core\Console\Commands\Migrations\RollbackCommand as MigrateRollbackCommand;
use Core\Console\Commands\Migrations\StatusCommand;
use Core\Console\Commands\Optimize\OptimizeCommand;
use Core\Console\Commands\Optimize\ViewCacheCommand;
use Core\Console\Commands\Route\RouteListCommand;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Foundation\Providers\ArtisanServiceProvider;

class ConsoleServiceProvider extends ArtisanServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->overrideFoundationCommands();
    }

    /**
     * Replace some of the illuminate/foundation
     * default commands with the
     * application commands.
     *
     * @return void
     */
    protected function overrideFoundationCommands()
    {
        // Override the AppNameCommand
        $this->app->singleton('command.app.name', function ($app) {
            return new AppNameCommand($app['composer'], $app['files']);
        });

        // Override the ModelMakeCommand
        $this->app->singleton('command.model.make', function ($app) {
            return new ModelMakeCommand($app['files']);
        });

        // Override the TestMakeCommand
        $this->app->singleton('command.test.make', function ($app) {
            return new TestMakeCommand($app['files']);
        });

        // Override the ServeCommand
        $this->app->extend('command.serve', function () {
            return new ServeCommand;
        });

        // Override the RouteListCommand
        $this->app->singleton('command.route.list', function ($app) {
            return new RouteListCommand($app['router'], $app['manifest:module']);
        });

        // Override the AuthMakeCommand
        $this->app->extend('command.make.auth', function () {
            return new AuthMakeCommand;
        });

        // Override the OptimizeCommand
        $this->app->extend('command.optimize', function ($app) {
            return new OptimizeCommand;
        });

        // Override the ViewCacheCommand
        $this->app->extend('command.view.cache', function ($app) {
            return new ViewCacheCommand;
        });

        // Register the Migrator class
        // before any migration-related commands.
        $this->app->singleton(Migrator::class, function ($app) {
            return $app['migrator'];
        });

        // Override the MigrateCommand
        $this->app->singleton('command.migrate', function ($app) {
            return new MigrateCommand($app['migrator']);
        });

        // Override the MakeMigrateCommand
        $this->app->singleton('command.make.migration', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];

            $composer = $app['composer'];

            return new MigrateMakeCommand($creator, $composer);
        });

        // Override the RollbackCommand
        $this->app->singleton('command.migrate.rollback', function ($app) {
            return new MigrateRollbackCommand($app['migrator']);
        });

        // Override the StatusCommand
        $this->app->singleton('command.migrate.status', function ($app) {
            return new StatusCommand($app['migrator']);
        });
    }
}
