<?php

namespace Core\Repositories\Contracts;

use Core\Application\Repository\RepositoryInterface;

interface SearchRepositoryInterface
{
    /**
     * Should retrieve the file from storage
     * and return the url string.
     *
     * @param  string $query
     * @return mixed
     */
    public function search($query);
}
