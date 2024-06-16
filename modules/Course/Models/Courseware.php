<?php

namespace Course\Models;

use Core\Models\Accessors\CommonAttributes;
use Core\Models\Relations\BelongsToUser;
use Core\Models\Scopes\Typeable;
use Course\Enumerations\MimetypeIconKeys;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Courseware extends Model
{
    use CommonAttributes,
        BelongsToUser,
        Searchable,
        SoftDeletes,
        Typeable;

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
    protected $typeSignature = 'courseware';

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
     * Retrieve the url string.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->uri;
    }

    /**
     * Retrieve the icon from path.
     *
     * @return string
     */
    public function getIconAttribute()
    {
        $extension = pathinfo($this->pathname, PATHINFO_EXTENSION);

        return MimetypeIconKeys::guess($extension);
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
