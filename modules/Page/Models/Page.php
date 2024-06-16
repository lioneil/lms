<?php

namespace Page\Models;

use Core\Enumerations\ModelStatus;
use Core\Models\Accessors\CommonAttributes;
use Core\Models\Publishable;
use Core\Models\Relations\BelongsToUser;
use Core\Models\Template;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Taxonomy\Models\Relations\BelongsToCategory;

class Page extends Model
{
    use BelongsToCategory,
        BelongsToUser,
        CommonAttributes,
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
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the template belonging to page.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Return template path.
     *
     * @return string
     */
    public function getTemplatepathAttribute()
    {
        return $this->template->pathname;
    }

    /**
     * Retrieve the status of the page.
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
