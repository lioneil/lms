<?php

namespace Setting\Models;

use Core\Models\Accessors\CommonAttributes;
use Core\Models\Relations\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Setting\Enumerations\SettingsKey;

class Setting extends Model
{
    use CommonAttributes,
        BelongsToUser;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'key';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * Retrieve the saved logo url.
     *
     * @return mixed
     */
    public function logo()
    {
        return $this->whereKey(SettingsKey::APP_LOGO)->first()
            ? $this->whereKey(SettingsKey::APP_LOGO)->first()->value
            : url('logo.png');
    }
}
