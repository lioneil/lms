<?php

use Core\Manifests\ModuleManifest;
use Illuminate\Database\Seeder;
use Symfony\Component\Finder\Finder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(ModuleManifest $manifest)
    {
        foreach (Finder::create()
        ->in(collect($manifest->manifest['enabled'])->filter(function ($module) {
            return is_dir("{$module['path']}/database/seeds");
        })->map(function ($module) {
            return "{$module['path']}/database/seeds";
        })->all())->name('*.php') as $seeder) {
            $this->call(basename($seeder->getFilename(), '.php'));
        }
    }
}
