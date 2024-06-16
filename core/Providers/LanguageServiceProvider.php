<?php

namespace Core\Providers;

use Core\Repositories\LanguageRepository;
use Illuminate\Support\ServiceProvider;

class LanguageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerJsonLanguageFile();
    }

    /**
     * Register the current locale's json file.
     *
     * @return void
     */
    protected function registerJsonLanguageFile()
    {
        try {
            $this->app->singleton('core.repository.language', function () {
                $locale = $this->app->getLocale();
                return new LanguageRepository(json_decode(
                    resource_path("lang/$locale.json"), true
                ), config('language.supported', []));
            });
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
