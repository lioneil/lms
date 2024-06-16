<?php

namespace Comment\Models\Relations;

use Comment\Models\Comment;

trait BelongsToParent
{
    /**
     * Gets the parent of the comment.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id', 'id');
    }
}
