<?php

namespace Comment\Models\Relations;

use Comment\Models\Comment;

trait HasManyChildComments
{
    /**
     * Gets the children comments for this resource.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id')->orderBy('updated_at', 'desc');
    }

    /**
     * Alias for children
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getRepliesAttribute()
    {
        return $this->children;
    }

    /**
     * Returns if the comments has replies.
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return $this->children->count();
    }

    /**
     * Returns if the comments has replies.
     *
     * @return boolean
     */
    public function hasReplies()
    {
        return $this->children->count();
    }
}
