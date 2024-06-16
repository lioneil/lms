<?php

namespace Subscription\Models;

use User\Models\User;

trait Subscribable
{
    /**
     * Indicates if the model can be subscribed to.
     *
     * @var boolean
     */
    public $subscribes = true;

    /**
     * The name of the "subscribed at" column.
     *
     * @var string
     */
    public static $subscribedAt = 'subscribed_at';

    /**
     * The name of the "unsubscribed at" column.
     *
     * @var string
     */
    public static $unsubscribedAt = 'unsubscribed_at';

    /**
     * Retrieve all subscriptions for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'subscribable');
    }

    /**
     * Have the authenticated or passed in user subscribe to the model.
     *
     * @param  \User\Models\User $user
     * @return boolean
     */
    public function subscribe(User $user = null)
    {
        $subscription = $this->subscriptions()->updateOrCreate([
            'user_id' => $user->id ?? auth()->id() ?? null,
        ], ['user_id' => $user->id ?? auth()->id(), 'name' => $this->getSubcriptionName()]);

        $subscription->{self::$subscribedAt} = $this->freshTimestamp();

        $subscription->save();

        $this->fireModelEvent('subscribed', $halt = false);

        return $subscription;
    }

    /**
     * Have the authenticated or a passed in user unsubscribe from the model.
     *
     * @param  \User\Models\User $user
     * @return boolean
     */
    public function unsubscribe(User $user = null)
    {
        $subscription = $this->subscriptions()
            ->where('user_id', $user->id ?? auth()->id())
            ->exists() ? $this->subscriptions()
            ->where('user_id', $user->id ?? auth()->id())
            ->first()->delete() : false;

        $this->fireModelEvent('unsubscribed', $halt = false);

        return $subscription;
    }

    /**
     * Get the users for the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscribers()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope a query of records subscribed by the given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  \User\Models\User                     $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubscribedBy($query, User $user)
    {
        return $query->whereHas('subscriptions', function ($query) use ($user) {
            $query->where('user_id', $user->getKey());
        });
    }

    /**
     * Determine if the model is subscribed by the given user.
     *
     * @param  \User\Models\User $user
     * @return boolean
     */
    public function isSubscribedBy(User $user)
    {
        return $this->subscriptions()->where('user_id', $user->getKey())->exists();
    }

    /**
     * Determin if the User id is subscibed to the resource.
     *
     * @param  integer $id
     * @return boolean
     */
    public function isSubscribedById(int $id)
    {
        return $this->isSubscribedBy(User::find($id));
    }

    /**
     * Determine if the model is subscribed by the given user.
     *
     * @param  \User\Models\User $user
     * @return boolean
     */
    public function isUnsubscribedBy(User $user)
    {
        return ! $this->subscriptions()->where('user_id', $user->getKey())->exists();
    }

    /**
     * Generate a subcription name for the model.
     *
     * @return string
     */
    protected function getSubcriptionName()
    {
        return sprintf(trans('Subcribed to "%s"'), $this->title ?? $this->name);
    }
}
