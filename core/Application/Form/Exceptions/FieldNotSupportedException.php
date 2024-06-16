<?php

namespace Core\Application\Form\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class FieldNotSupportedException extends Exception
{
    /**
     * Report the resource requested for download is
     * currently restricted.
     *
     * @return void
     */
    public function report()
    {
        Log::debug('Unsupported field.');
    }
}
