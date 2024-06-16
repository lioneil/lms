<?php

namespace Course\Observers;

use Course\Events\LessonDeleted;
use Course\Events\LessonSaved;
use Course\Models\Content;
use Course\Services\ContentServiceInterface;

class ContentObserver
{
    /**
     * The ContentService instance.
     *
     * @var \Course\Services\ContentServiceInterface
     */
    protected $service;

    /**
     * Initialize service class.
     */
    public function __construct()
    {
        $this->service = app(ContentServiceInterface::class);
    }

    /**
     * Listen to the Content created event.
     *
     * @param  \Course\Models\Content $resource
     * @return void
     */
    public function created(Content $resource)
    {
        $this->service->closureAttachToSelf($resource);
    }

    /**
     * Listen to the Content reordered event.
     *
     * @param  \Course\Models\Content $resource
     * @return void
     */
    public function reordered(Content $resource)
    {
        $this->service->closureUpdateParent($resource);
    }
}
