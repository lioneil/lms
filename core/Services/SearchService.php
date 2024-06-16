<?php

namespace Core\Services;

use Core\Application\Service\Service;

class SearchService extends Service implements SearchServiceInterface
{
    /**
     * Constructor to bind model to a repository.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
