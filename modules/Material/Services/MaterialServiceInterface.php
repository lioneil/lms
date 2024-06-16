<?php

namespace Material\Services;

use Core\Application\Service\Contracts\Uploadable;
use Core\Application\Service\ServiceInterface;
use Illuminate\Http\UploadedFile;

interface MaterialServiceInterface extends ServiceInterface, Uploadable
{

}
