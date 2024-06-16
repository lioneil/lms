<?php

namespace Assessment\Models;

use Assessment\Models\Field;
use Assessment\Models\Submission;
use Core\Models\Accessors\CommonAttributes;
use Core\Models\Relations\BelongsToUser;
use Illuminate\Database\Eloquent\Concerns\hasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Template\Models\Relations\BelongsToTemplate;

class Assessment extends Model
{
    use BelongsToUser,
        BelongsToTemplate,
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
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'forms';

    /**
     * Get the fields for the assessment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields()
    {
        return $this->hasMany(Field::class, 'form_id')->orderBy('sort');
    }

    /**
     * Get the owning submission model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function submissions()
    {
        return $this->morphMany(Submission::class, 'submissible');
    }
}
