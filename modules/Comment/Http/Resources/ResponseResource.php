<?php

namespace Comment\Http\Resources;

use Comment\Http\Resources\ReplyResource;
use Illuminate\Http\Resources\Json\JsonResource;
use User\Http\Resources\User as UserResource;

class ResponseResource extends JsonResource
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
            'replies' => ReplyResource::collection($this->replies),
            'user' => new UserResource($this->user),
        ]);
    }
}
