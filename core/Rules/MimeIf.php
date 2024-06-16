<?php

namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;
use Mimey\MimeTypes;

class MimeIf implements Rule
{
    /**
     * The mimetypes of the passed in string.
     *
     * @var array
     */
    protected $mimetypes;

    /**
     * Create a new rule instance.
     *
     * @param  string $type
     * @return void
     */
    public function __construct(string $type = null)
    {
        $this->mimes = new MimeTypes;
        $this->mimetypes = $this->mimes->getAllMimeTypes($type);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return boolean
     */
    public function passes($attribute, $value)
    {
        if (empty($this->mimetypes)) {
            return true;
        }

        if ($attribute && is_file($value)) {
            $extension = $value->getClientOriginalExtension();
        }

        if (is_string($value)) {
            $extension = pathinfo($value, PATHINFO_EXTENSION);
        }

        $mimeType = $this->mimes->getMimeType($extension);

        return in_array($mimeType, $this->mimetypes);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
