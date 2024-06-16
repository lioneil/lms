<?php

namespace Announcement\Http\Requests;

use Announcement\Services\AnnouncementServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            AnnouncementServiceInterface::class
        )->authorize($this->announcement, 'announcements');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->container->make(
            AnnouncementServiceInterface::class
        )->rules($this->announcement);
    }
}
