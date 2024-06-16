<?php

namespace Course\Models;

use Taxonomy\Models\Tag as Model;

class Tag extends Model
{
    /**
     * Get all of the courses that are assigned this tag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function courses()
    {
        return $this->morphedByMany(Course::class, 'taggable');
    }
}
