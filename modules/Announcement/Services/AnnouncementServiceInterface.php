<?php

namespace Announcement\Services;

use Core\Application\Service\Contracts\Uploadable;
use Core\Application\Service\ServiceInterface;
use Ilumminate\Http\UploadedFile;

interface AnnouncementServiceInterface extends ServiceInterface, Uploadable
{

}
