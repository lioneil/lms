<?php

namespace Taxonomy\Models\Relations;

use Taxonomy\Models\Tag;

trait MorphToManyTags
{
    /**
     * Get all of the tags for the resource.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Retrieve the tags for this resource.
     *
     * @return string
     */
    public function getTagAttribute()
    {
        return $this->tags->implode('name', '/');
    }

    /**
     * Retrieve the tags for this resource.
     *
     * @param  string $key
     * @return string
     */
    public function getTagsAsArray($key = 'name')
    {
        return $this->tags->pluck($key)->toArray();
    }
}
