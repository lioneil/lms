<?php

namespace Core\Providers;

use Core\Providers\BaseServiceProvider;
use Setting\Models\Setting;
use Setting\Services\SettingService;
use Setting\Services\SettingServiceInterface;

class SettingServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->bind(SettingServiceInterface::class, SettingService::class);

        $this->app->singleton('settings', function () {
            return new SettingService(new Setting, $this->app['request'], config('settings'));
        });
    }
}
