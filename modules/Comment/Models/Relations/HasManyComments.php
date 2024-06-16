<?php

namespace Comment\Models\Relations;

use Comment\Models\Comment;

trait HasManyComments
{
    /**
     * Gets the resources that belongs to this resource.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
