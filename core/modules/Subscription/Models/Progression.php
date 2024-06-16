<?php

namespace Subscription\Models;

use Core\Models\Relations\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Subscription\Events\UserProgressed;

class Progression extends Model
{
    use BelongsToUser;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'collection',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'progressed' => UserProgressed::class,
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get all of the owning progressionable models.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function progressionable()
    {
        return $this->morphTo();
    }
}
