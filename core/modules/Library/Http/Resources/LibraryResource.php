<?php

namespace Library\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use User\Http\Resources\User as UserResource;

class LibraryResource extends JsonResource
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
            'author' => $this->author,
            'created' => $this->created,
            'deleted' => $this->deleted,
            'modified' => $this->modified,
            'user' => new UserResource($this->user),
        ]);
    }
}
