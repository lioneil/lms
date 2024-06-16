<?php

use Illuminate\Database\Seeder;
use Setting\Services\SettingServiceInterface;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(SettingServiceInterface $settings)
    {
        foreach (settings()->all() as $key => $value) {
            $settings->updateOrCreate(['key' => $key], [
                'value' => $value,
            ]);
        }
    }
}
