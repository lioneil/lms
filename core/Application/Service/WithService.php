<?php

namespace Core\Application\Service;

use Core\Application\Service\Service;
use Illuminate\Config\Repository;

trait WithService
{
    /**
     * The service attribute.
     *
     * @var \Core\Application\Service\Service
     */
    protected $service;

    /**
     * Retrieve the service instance.
     *
     * @return \Core\Application\Service\Service
     * @throws \Exception Throws a service not found error.
     */
    public function service()
    {
        if ($this->service instanceof Service) {
            return $this->service;
        }

        if ($this->service instanceof Repository) {
            return $this->service;
        }

        throw new \Exception("{$this->service} is not instance of \Core\Application\Service\Service.", 1);
    }
}
