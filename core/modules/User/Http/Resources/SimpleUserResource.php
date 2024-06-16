<?php

namespace User\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use User\Http\Resources\Detail as DetailResource;

class SimpleUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'avatar' => $this->avatar,
            'birthday' => $this->detail('Birthday'),
            'created' => $this->created,
            'deleted' => $this->deleted,
            'details' => $this->getDetails(),
            'details:common' => $this->getCommonDetails(),
            'details:others' => $this->getOtherDetails(),
            'displayname' => $this->displayname,
            'is:superadmin' => $this->isSuperAdmin(),
            'modified' => $this->modified,
            'role' => $this->role,
        ]);
    }

    /**
     * Retrieve the common and other user details
     * combined.
     *
     * @return array
     */
    protected function getDetails()
    {
        return array_merge($this->getCommonDetails()->mapWithKeys(function ($detail) {
            return [$detail->key => $detail];
        })->toArray(), ['others' => $this->getOtherDetails()->mapWithKeys(function ($detail) {
            return [$detail->key => $detail];
        })->toArray()]);
    }
}
