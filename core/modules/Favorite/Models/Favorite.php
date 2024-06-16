<?php

namespace Favorite\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    /**
     * Fillable fieldsfor a favorite.
     *
     * @var array
     */
    protected $fillable = ['user_id'];
}
