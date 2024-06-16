<?php

namespace User\Models;

use Core\Enumerations\DetailType;
use Core\Models\Accessors\CommonAttributes;
use Illuminate\Database\Eloquent\Model;
use User\Enumerations\CredentialColumns;
use User\Models\User;

class Detail extends Model
{
    use CommonAttributes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['text'];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the user that owns the details.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the detail type is account
     * and the column key is password.
     *
     * @return boolean
     */
    public function isPasswordField(): bool
    {
        return $this->attributes['key'] == CredentialColumns::PASSWORD
            && $this->attributes['type'] == DetailType::ACCOUNT;
    }

    /**
     * Retrieve the dummy password string.
     *
     * @return string
     */
    public function getPasswordAttribute(): string
    {
        return str_repeat("*", strlen($this->attributes['value']));
    }

    /**
     * Retrieve the parsed value string.
     *
     * @return string
     */
    public function getTextAttribute():? string
    {
        switch (strtolower($this->key)) {
            case DetailType::BIRTHDAY:
                if (! is_null($this->value)) {
                    return date(settings('format:date:birthday', 'd-M, Y'), strtotime($this->value));
                }
                break;

            default:
                return $this->value;
                break;
        }

        return $this->value;
    }
}
