<?php

namespace Test\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'avatar' => $this->avatar,
            'birthday' => $this->detail('Birthday'),
            'created' => $this->created,
            'deleted' => $this->deleted,
            'displayname' => $this->displayname,
            'modified' => $this->modified,
            'role' => $this->role,
        ]);
    }
}
