<?php

namespace Assessment\Services;

use Core\Application\Service\Contracts\Exportable;
use Core\Application\Service\Contracts\Importable;
use Core\Application\Service\ServiceInterface;
use Assessment\Models\Assessment;

interface AssessmentServiceInterface extends ServiceInterface, Exportable, Importable
{
    // Put all required methods here.
}
