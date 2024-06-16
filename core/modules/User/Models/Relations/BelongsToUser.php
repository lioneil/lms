<?php

namespace Core\Models\Relations;

use User\Models\User;

trait BelongsToUser
{
    /**
     * The user id key.
     *
     * @var string
     */
    protected $userId = 'user_id';

    /**
     * Retrieve the user that this resource belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, $this->userId);
    }

    /**
     * Retrieve the user id key value.
     *
     * @return string
     */
    public function getUserKeyName()
    {
        return $this->userId;
    }

    /**
     * Retrieve the key of the user this resource belongs to.
     *
     * @return integer
     */
    public function getUserKey(): int
    {
        return $this->{$this->getUserKeyName()};
    }
}
