<?php

namespace Core\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WidgetNotFoundErrorException extends Exception
{
    /**
     * Report the resource requested for download is
     * currently restricted.
     *
     * @return void
     */
    public function report()
    {
        Log::error('Restricted resource.');
    }
}
