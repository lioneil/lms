<?php

namespace Subscription\Models;

use Subscription\Models\Progression;
use User\Models\User;

trait Progressible
{
    /**
     * The user_id key.
     *
     * @var string
     */
    protected $userIdKey = 'user_id';

    /**
     * Retrieve all subscriptions for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function progressions()
    {
        return $this->morphMany(Progression::class, 'progressionable');
    }

    /**
     * Retrieve the passed in User's progressions.
     *
     * @param  \User\Models\?User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function progressionsOf(?User $user)
    {
        return $this->progressions()->where(
            $this->userIdKey, $user->id ?? null
        );
    }
}
