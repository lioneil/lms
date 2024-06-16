<?php

namespace Core\Providers;

use Core\Providers\BaseServiceProvider;
use Core\Repositories\SearchRepository;

class SearchServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->singleton(SearchRepository::class, function () {
            return new SearchRepository($this->app['request'], config('scout.indices'));
        });
    }
}
