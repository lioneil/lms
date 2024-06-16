<?php

namespace Announcement\Models;

use Core\Models\Accessors\CommonAttributes;
use Core\Models\Publishable;
use Core\Models\Relations\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Taxonomy\Models\Relations\BelongsToCategory;

class Announcement extends Model
{
    use CommonAttributes,
        BelongsToUser,
        BelongsToCategory,
        Publishable,
        SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'published_at',
        'drafted_at',
        'expired_at',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string
     */
    protected $guarded = [];

    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'announcements';
}
