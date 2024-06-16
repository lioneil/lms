<?php

namespace Taxonomy\Models;

use Core\Models\Accessors\CommonAttributes;
use Core\Models\Relations\BelongsToUser;
use Core\Models\Scopes\Typeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use CommonAttributes,
        BelongsToUser,
        Searchable,
        SoftDeletes,
        Typeable;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'categories';
}
