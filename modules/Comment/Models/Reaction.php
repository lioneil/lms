<?php

namespace Comment\Models;

use Core\Models\Accessors\CommonAttributes;
use Core\Models\Relations\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use BelongsToUser,
        CommonAttributes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get all of the owning reactable models.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function reactable()
    {
        return $this->morphTo();
    }
}
