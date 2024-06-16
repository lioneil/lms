<?php

namespace Mail\Services;

use Core\Application\Service\ServiceInterface;

interface MailServiceInterface
{
    /**
     * Retrieve all resources and paginate.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function list();
}
