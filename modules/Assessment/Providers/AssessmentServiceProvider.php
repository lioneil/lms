<?php

namespace Assessment\Providers;

use Assessment\Providers\EventServiceProvider;
use Assessment\Services\AssessmentService;
use Assessment\Services\AssessmentServiceInterface;
use Assessment\Services\FieldService;
use Assessment\Services\FieldServiceInterface;
use Assessment\Services\StatisticService;
use Assessment\Services\StatisticServiceInterface;
use Assessment\Services\SubmissionService;
use Assessment\Services\SubmissionServiceInterface;
use Core\Providers\BaseServiceProvider;

class AssessmentServiceProvider extends BaseServiceProvider
{
    /**
     * Array of providers to register.
     *
     * @var array
     */
    protected $providers = [
        EventServiceProvider::class,
    ];

    /**
     * Register any service class with its interface.
     *
     * @return void
     */
    protected function registerServiceBindings()
    {
        $this->app->bind(AssessmentServiceInterface::class, AssessmentService::class);
        $this->app->bind(SubmissionServiceInterface::class, SubmissionService::class);
        $this->app->bind(StatisticServiceInterface::class, StatisticService::class);
        $this->app->bind(FieldServiceInterface::class, FieldService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
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
}
