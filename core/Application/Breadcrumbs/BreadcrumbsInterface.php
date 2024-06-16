<?php

namespace Core\Application\Breadcrumbs;

interface BreadcrumbsInterface
{
    /**
     * Retrieve a segment instance from
     * array of segments.
     *
     * @param  integer $position
     * @return string
     */
    public function segment(int $position = 0);
}
