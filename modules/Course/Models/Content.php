<?php

namespace Course\Models;

use Codrasil\Closurable\Closurable;
use Comment\Models\Relations\MorphManyComments;
use Core\Models\Accessors\CommonAttributes;
use Core\Models\Relations\BelongsToUser;
use Course\Models\Lesson;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Lesson
{
    use BelongsToUser,
        CommonAttributes,
        Closurable,
        SoftDeletes;

    /**
     * The property on class instances.
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string
     */
    protected $guarded = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lessons';
}
