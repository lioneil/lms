<?php

namespace Comment\Models\Relations;

use Comment\Models\Comment;

trait BelongsToManyComments
{
    /**
     * Retrieve the comments for the resource.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function comments()
    {
        return $this->belongsToMany(Comment::class);
    }
}
