<?php

namespace Favorite\Models;

use User\Models\User;

trait Favoritable
{
    /**
     * Fetch all favorites for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    /**
     * Have the authenticated or passed in user favorite the model.
     *
     * @param  \User\Models\User $user
     * @return boolean
     */
    public function favorite(User $user = null)
    {
        return $this->favorites()->updateOrCreate([
            'user_id' => $user->id ?? auth()->id() ?? null,
        ], ['user_id' => $user->id ?? auth()->id()]);
    }

    /**
     * Have the authenticated or a passed in user unfavorite the model.
     *
     * @param  \User\Models\User $user
     * @return boolean
     */
    public function unfavorite(User $user = null)
    {
        return $this->favorites()
            ->where('user_id', $user->id ?? auth()->id())
            ->exists() ? $this->favorites()
            ->where('user_id', $user->id ?? auth()->id())
            ->first()->delete() : false;
    }

    /**
     * Scope a query to records favorited by the given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  \User\Models\User                     $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFavoritedBy($query, User $user)
    {
        return $query->whereHas('favorites', function ($query) use ($user) {
            $query->where('user_id', $user->getKey());
        });
    }

    /**
     * Determine if the model is favorited by the given user.
     *
     * @param  \User\Models\User $user
     * @return boolean
     */
    public function isFavoritedBy(User $user)
    {
        return $this->favorites()
            ->where('user_id', $user->getKey())
            ->exists();
    }
}
