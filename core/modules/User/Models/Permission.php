<?php

namespace User\Models;

use Core\Models\Accessors\CommonAttributes;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Permission extends Model
{
    use CommonAttributes,
        Searchable;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * User exposed observable events.
     *
     * These are extra user-defined events observers may subscribe to.
     *
     * @var array
     */
    protected $observables = [
        'refreshed',
    ];

    /**
     * Update or create new permissions
     * from storage.
     *
     * @return void
     */
    public function refresh()
    {
        $this->fireModelEvent('refreshed', false);
    }
}
