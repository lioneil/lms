<?php

namespace Comment\Models;

use Comment\Models\CanBeVotedTrait;
use Comment\Models\Reactable;
use Comment\Models\Relations\BelongsToParent;
use Comment\Models\Relations\HasManyChildComments;
use Comment\Models\Scopes\ApprovedScope;
use Core\Models\Accessors\CommonAttributes;
use Core\Models\Relations\BelongsToUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use BelongsToParent,
        BelongsToUser,
        CanBeVotedTrait,
        CommonAttributes,
        HasManyChildComments,
        Reactable,
        SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'approved_at',
        'locked_at',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ApprovedScope);
    }

    /**
     * Gets all categories via given category type.
     *
     * @param  Illuminate\Database\Eloquent\Builder $builder
     * @return Illuminate\Database\Eloquent\Model
     */
    public function scopeParents(Builder $builder)
    {
        return $builder->whereNull('parent_id');
    }

    /**
     * Get all of the owning commentable models.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Morph to many reactions.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    /**
     * Approve the comment.
     *
     * @return void
     */
    public function approve()
    {
        $this->approved_at = $this->freshTimestamp();
        $this->save();
    }

    /**
     * Determine if comment is approved.
     *
     * @return boolean
     */
    public function isApproved()
    {
        return ! is_null($this->approved_at);
    }

    /**
     * Retrieve the approve status.
     *
     * @return boolean
     */
    public function getApprovedAttribute()
    {
        return $this->isApproved();
    }
}
