<?php

namespace Course\Services;

use Core\Application\Service\Contracts\Exportable;
use Core\Application\Service\Contracts\Importable;
use Core\Application\Service\Contracts\Uploadable;
use Core\Application\Service\ServiceInterface;
use Course\Models\Course;
use Illuminate\Http\UploadedFile;

interface CourseServiceInterface extends ServiceInterface, Exportable, Importable, Uploadable
{

}
