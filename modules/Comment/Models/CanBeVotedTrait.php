<?php

namespace Comment\Models;

trait CanBeVotedTrait
{
    /**
     * Check if upvotes has value.
     *
     * @return boolean
     */
    public function hasUpVotes()
    {
        return ! is_null($this->upvotes) && $this->upvotes;
    }
}
