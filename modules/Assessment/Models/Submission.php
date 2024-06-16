<?php

namespace Assessment\Models;

use Core\Models\Accessors\CommonAttributes;
use Core\Models\Relations\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use BelongsToUser,
        CommonAttributes,
        SoftDeletes;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'collection',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'submissions';

    /**
     * Get the owning submissible model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function submissible()
    {
        return $this->morphTo();
    }
}
