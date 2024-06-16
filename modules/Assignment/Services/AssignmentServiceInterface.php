<?php

namespace Assignment\Services;

use Core\Application\Service\Contracts\Uploadable;
use Core\Application\Service\ServiceInterface;
use Illuminate\Http\UploadedFile;

interface AssignmentServiceInterface extends ServiceInterface, Uploadable
{

}
