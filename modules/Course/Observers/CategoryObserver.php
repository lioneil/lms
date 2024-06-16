<?php

namespace Course\Observers;

use Course\Services\CourseServiceInterface;
use Taxonomy\Models\Category;

class CategoryObserver
{
    /**
     * The ContentService instance.
     *
     * @var \Course\Services\CourseServiceInterface
     */
    protected $service;

    /**
     * Initialize service class.
     *
     * @param \Course\Services\CourseServiceInterface $service
     */
    public function __construct(CourseServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Listen to the Content deleted event.
     *
     * @param  \Taxonomy\Models\Category $category
     * @return void
     */
    public function deleted(Category $category)
    {
        $this->service->whereCategoryId($category->getKey())->get()->each(function ($course) {
            $course->removeCategory();
        });
    }
}
