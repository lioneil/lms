<?php

namespace Quiz\Models;

use Core\Models\Relations\BelongsToUser;
use Template\Models\Relations\BelongsToTemplate;

class Quiz extends Form
{
    use BelongsToTemplate;

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
    protected $table = 'forms';
}
