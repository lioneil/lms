<?php

namespace Core\Application\Repository;

trait WithRepository
{
    /**
     * The repository attribute.
     *
     * @var \Core\Application\Repository\Repository
     */
    protected $repository;

    /**
     * Retrieve the repository instance.
     *
     * @return mixed
     */
    public function repository()
    {
        return $this->repository ?? null;
    }
}
