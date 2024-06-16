<?php

namespace Comment\Models;

use Comment\Enumerations\ReactionKeys;
use Illuminate\Support\Facades\Auth;
use User\Models\User;

trait Reactable
{
    /**
     * Retrieve the reactions for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reactions()
    {
        return $this->morphMany(Reaction::class);
    }

    /**
     * Like the model.
     *
     * @param  \User\Models\User $user
     * @return \Reaction\Models\Reaction
     */
    public function like(User $user = null)
    {
        $reactable = $this->reactions()->updateOrCreate([
           'user_id' => $user->id ?? Auth::id(),
        ], ['user_id' => $user->id ?? Auth::id(), 'vote' => ReactionKeys::LIKE]);

        $this->fireModelEvent('liked', $halt = false);

        return $reactable;
    }

    /**
     * Dislike the model.
     *
     * @param  \User\Models\User $user
     * @return \Reaction\Models\Reaction
     */
    public function dislike(User $user = null)
    {
        $reactable = $this->reactions()->updateOrCreate([
           'user_id' => $user->id ?? Auth::id(),
        ], ['user_id' => $user->id ?? Auth::id(), 'vote' => ReactionKeys::DISLIKE]);

        $this->fireModelEvent('disliked', $halt = false);

        return $reactable;
    }

    /**
     * Check if resource is liked by the given user.
     *
     * @param  \User\Models\User $user
     * @return boolean
     */
    public function likedBy(User $user): bool
    {
        return $this->reactions()
            ->whereUserId($user->getKey())
            ->whereVote(ReactionKeys::LIKE)
            ->exists();
    }

    /**
     * Check if resource is disliked by the given user.
     *
     * @param  \User\Models\User $user
     * @return boolean
     */
    public function dislikedBy(User $user): bool
    {
        return $this->reactions()
            ->whereUserId($user->getKey())
            ->whereVote(ReactionKeys::DISLIKE)
            ->exists();
    }
}
