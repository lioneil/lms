<?php

namespace Core\Application\Service\Helpers;

use Core\Enumerations\Role as RoleCode;
use Core\Support\Facades\ModuleManifest;

trait HaveDefaultConfig
{
    /**
     * Retrieve all roles from registered
     * modules found in config/roles.php.
     *
     * @param  string $file
     * @return \Illuminate\Support\Collection
     */
    public function getDefaultConfigFromFile(string $file = null)
    {
        return ModuleManifest::modules()->filter(function ($module) use ($file) {
            return file_exists("{$module['path']}/$file");
        })->map(function ($module) use ($file) {
            return ModuleManifest::getRequire("{$module['path']}/$file");
        });
    }
}
