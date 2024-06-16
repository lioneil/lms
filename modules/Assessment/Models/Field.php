<?php

namespace Assessment\Models;

use Assessment\Models\Assessment;
use Core\Models\Accessors\CommonAttributes;
use Illuminate\Database\Eloquent\Concerns\belongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Field extends Model
{
    use CommonAttributes;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sort' => 'integer',
        'metadata' => 'collection',
    ];

    /**
     * The sort key string.
     *
     * @var string
     */
    protected $sortKey = 'sort';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Array of our custom model events declared under
     * model property $observables.
     *
     * @var array
     */
    protected $observables = [
        'reordered',
    ];

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'fields';

    /**
     * Retrieve the assessment that this resource belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'form_id');
    }

    /**
     * Save and trigger the reorder model event.
     *
     * @return $this
     */
    public function reorder()
    {
        $this->save();

        $this->fireModelEvent('reordered', $halt = false);

        return $this;
    }

    /**
     * Retrieve the short version text of
     * the resource's description field.
     *
     * @return string
     */
    public function getExcerptAttribute()
    {
        return Str::words(
            strip_tags(
                $this->attributes['title']
                ?? $this->attributes['description']
                ?? $this->attributes['body']
                ?? null
            ),
            settings('display:excerpt', 15),
            '...'
        );
    }
}
