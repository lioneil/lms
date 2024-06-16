<?php

namespace User\Services;

use Core\Application\Service\ServiceInterface;

interface UserServiceInterface extends ServiceInterface
{
    /**
     * Generate random hash key.
     *
     * @param  string $key
     * @return string
     */
    public function hash(string $key);
}
