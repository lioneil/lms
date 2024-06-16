<?php

namespace Mail\Providers;

use Core\Providers\BaseServiceProvider;
use Mail\Services\MailService;
use Mail\Services\MailServiceInterface;

class MailServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MailServiceInterface::class, MailService::class);
    }
}
