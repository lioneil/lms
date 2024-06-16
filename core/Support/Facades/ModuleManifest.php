<?php

namespace Core\Support\Facades;

use Illuminate\Support\Facades\Facade;

class ModuleManifest extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'manifest:module';
    }
}
