<?php

namespace Core\Services;

use Core\Application\Service\ServiceInterface;

interface SearchServiceInterface extends ServiceInterface
{
    /**
     * Generate random hash key.
     *
     * @param  string $key
     * @return self
     */
    public function search(string $key);
}
