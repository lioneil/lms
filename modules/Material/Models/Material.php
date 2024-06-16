<?php

namespace Material\Models;

use Core\Enumerations\ModelStatus;
use Core\Models\Accessors\CommonAttributes;
use Core\Models\Publishable;
use Core\Models\Relations\BelongsToUser;
use Core\Models\Scopes\TypeScope;
use Course\Models\Courseware;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Material extends Courseware
{
    use CommonAttributes,
        BelongsToUser,
        Publishable,
        Searchable,
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
    protected $table = 'coursewares';

    /**
     * The model type.
     *
     * @var string
     */
    protected $typeSignature = 'material';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TypeScope);
    }

    /**
     * Retrieve the type signature.
     *
     * @return string
     */
    public function getTypeSignature()
    {
        return $this->typeSignature;
    }

    /**
     * Get the owning coursewareable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function coursewareable()
    {
        return $this->morphTo();
    }

    /**
     * Retrieve the status of the material.
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        $status = ModelStatus::UNPUBLISHED;

        if ($this->isPublished()) {
            $status = ModelStatus::PUBLISHED;
        }

        if ($this->isDrafted()) {
            $status = ModelStatus::DRAFTED;
        }

        if ($this->trashed()) {
            $status = ModelStatus::TRASHED;
        }

        return Str::title($status);
    }
}
