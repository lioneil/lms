<?php

namespace Setting\Services;


// use Core\Application\Service\ServiceInterface;

use Illuminate\Contracts\Config\Repository as RepositoryInterface;
use Illuminate\Http\Request;
use Setting\Services\SettingService;

interface SettingServiceInterface extends RepositoryInterface
{
    //interface SettingServiceInterface extends ServiceInterface
    /**
     * Filter the items through regex with the given string.
     *
     * @param  string $key
     * @return mixed
     */
    public function containsKey($key);
}
