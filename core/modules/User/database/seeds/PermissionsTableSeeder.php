<?php

use Illuminate\Database\Seeder;
use User\Services\PermissionServiceInterface;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(PermissionServiceInterface $service)
    {
        $service->refresh();
    }
}
