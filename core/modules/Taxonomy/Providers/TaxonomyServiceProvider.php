<?php

namespace Taxonomy\Providers;

use Core\Providers\BaseServiceProvider;
use Taxonomy\Services\TagService;
use Taxonomy\Services\TagServiceInterface;
use Taxonomy\Services\TaxonomyService;
use Taxonomy\Services\TaxonomyServiceInterface;

class TaxonomyServiceProvider extends BaseServiceProvider
{
    /**
     * Array of providers to register.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TaxonomyServiceInterface::class, TaxonomyService::class);
        $this->app->bind(TagServiceInterface::class, TagService::class);
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
}
