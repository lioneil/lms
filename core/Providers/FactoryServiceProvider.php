<?php

namespace Core\Providers;

use Core\Manifests\ModuleManifest;
use Core\Providers\BaseServiceProvider;

class FactoryServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap all application factory files
     * from each modules.
     *
     * @return void
     */
    public function factories()
    {
        return $this->app[ModuleManifest::class]->modules()->reject(function ($module) {
            return ! is_dir("{$module['path']}/database/factories");
        })->map(function ($module) {
            return "{$module['path']}/database/factories";
        })->toArray();
    }
}
