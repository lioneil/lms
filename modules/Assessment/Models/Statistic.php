<?php

namespace Assessment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Statistic extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $cast = [
        'metadata' => 'json',
    ];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
