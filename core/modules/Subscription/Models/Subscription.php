<?php

namespace Subscription\Models;

use Core\Models\Relations\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Subscription\Events\UserSubscribed;
use Subscription\Events\UserUnsubscribed;

class Subscription extends Model
{
    use BelongsToUser;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'subscribed_at',
        'unsubscribed_at',
        'expired_at',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => UserSubscribed::class,
        'deleted' => UserUnsubscribed::class,
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get all of the owning subscribable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subscribable()
    {
        return $this->morphTo();
    }
}
