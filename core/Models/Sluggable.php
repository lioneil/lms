<?php

namespace Core\Models;

trait Sluggable
{
    /**
     * Retrieve the slug value.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug ?? $this->code;
    }
}
