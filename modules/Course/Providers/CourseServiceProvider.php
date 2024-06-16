<?php

namespace Course\Providers;

use Core\Providers\BaseServiceProvider;
use Course\Console\Commands\CourseCleanCommand;
use Course\Jobs\DeleteExpiredCourses;
use Course\Models\Content;
use Course\Models\Course;
use Course\Observers\CategoryObserver;
use Course\Observers\ContentObserver;
use Course\Observers\CourseObserver;
use Course\Services\CategoryService;
use Course\Services\CategoryServiceInterface;
use Course\Services\ContentService;
use Course\Services\ContentServiceInterface;
use Course\Services\CourseService;
use Course\Services\CourseServiceInterface;
use Course\Services\LessonService;
use Course\Services\LessonServiceInterface;
use Illuminate\Console\Scheduling\Schedule;
use Taxonomy\Models\Category;

class CourseServiceProvider extends BaseServiceProvider
{
    /**
     * Array of observable models.
     *
     * @var array
     */
    protected $observables = [
        [Course::class => CourseObserver::class],
        [Content::class => ContentObserver::class],
        [Category::class => CategoryObserver::class],
    ];

    /**
     * Array of providers to register.
     *
     * @var array
     */
    protected $providers = [
        EventServiceProvider::class,
    ];

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->bindRouteParameters();

        $this->bootScheduledJobs();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerConsoleCommands();
    }

    /**
     * Bind the alias parameter to the core.widget class.
     *
     * @return void
     */
    protected function bindRouteParameters()
    {
        $this->app['router']->bind('courseslug', function ($value) {
            return Course::withTrashed()->whereSlug($value)->firstOrFail();
        });

        $this->app['router']->bind('contentslug', function ($value) {
            return Content::withTrashed()->whereSlug($value)->firstOrFail();
        });
    }

    /**
     * Register any service class with its interface.
     *
     * @return void
     */
    protected function registerServiceBindings()
    {
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(ContentServiceInterface::class, ContentService::class);
        $this->app->bind(ContentServiceInterface::class, ContentService::class);
        $this->app->bind(CourseServiceInterface::class, CourseService::class);
        $this->app->bind(LessonServiceInterface::class, LessonService::class);
    }

    /**
     * Retrieve the array of factories.
     *
     * @return array
     */
    public function factories()
    {
        return [
            $this->directory('database/factories'),
        ];
    }

    /**
     * Get the base directory.
     *
     * @param  string $path
     * @return string
     */
    protected function directory(string $path = '')
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.$path;
    }

    /**
     * Register the module's console commands.
     *
     * @return void
     */
    protected function registerConsoleCommands()
    {
        $this->commands([
            CourseCleanCommand::class,
        ]);
    }

    /**
     * Register the scheduled jobs.
     *
     * @return void
     */
    public function bootScheduledJobs()
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->job(new DeleteExpiredCourses)->everyMinute();
        });
    }
}
