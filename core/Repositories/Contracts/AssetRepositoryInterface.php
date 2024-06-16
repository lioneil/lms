<?php

namespace Core\Repositories\Contracts;

use Core\Application\Repository\RepositoryInterface;

interface AssetRepositoryInterface extends RepositoryInterface
{
    /**
     * Should retrieve the file from storage
     * and return the url string.
     *
     * @param  string $file
     * @return string
     */
    public function fetch();
}
