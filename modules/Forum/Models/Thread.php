<?php

namespace Forum\Models;

use Comment\Models\Reactable;
use Comment\Models\Reaction;
use Comment\Models\Relations\MorphManyComments;
use Core\Models\Accessors\CommonAttributes;
use Core\Models\Relations\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Taxonomy\Models\Relations\BelongsToCategory;

class Thread extends Model
{
    use BelongsToCategory,
        BelongsToUser,
        CommonAttributes,
        MorphManyComments,
        Reactable,
        SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Retrieve the top level comments.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getResponsesAttribute()
    {
        return $this->comments()->parents()->get();
    }

    /**
     * Morph to many reactions
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }
}
