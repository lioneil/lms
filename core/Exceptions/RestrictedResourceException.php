<?php

namespace Core\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RestrictedResourceException extends Exception
{
    /**
     * Report the resource requested for download is
     * currently restricted.
     *
     * @return void
     */
    public function report()
    {
        Log::debug('Restricted resource.');
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return abort(Response::HTTP_UNAUTHORIZED);
    }
}
