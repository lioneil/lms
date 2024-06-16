<?php

namespace Taxonomy\Models;

use Illuminate\Database\Eloquent\Model;

class Taggable extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
