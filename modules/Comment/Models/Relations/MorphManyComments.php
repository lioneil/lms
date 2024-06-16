<?php

namespace Comment\Models\Relations;

use Comment\Models\Comment;

trait MorphManyComments
{
    /**
     * Morph to many comments.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->parents()->orderBy('updated_at', 'desc');
    }
}
