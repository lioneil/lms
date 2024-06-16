<?php

namespace Assignment\Models;

use Core\Models\Accessors\CommonAttributes;
use Core\Models\Relations\BelongsToUser;
use Core\Models\Scopes\TypeScope;
use Core\Models\Scopes\Typeable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Assignment extends Model
{
    use CommonAttributes,
        BelongsToUser,
        Typeable,
        Searchable,
        SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
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
    protected $typeSignature = 'assignment';

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
}
