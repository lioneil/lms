<?php

namespace Comment\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use User\Http\Resources\SimpleUserResource;

class CommentResource extends JsonResource
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
            'approved' => $this->approved,
            'author' => $this->author,
            'created' => $this->created,
            'deleted' => $this->deleted,
            'modified' => $this->modified,
            'replies' => CommentResource::collection($this->replies),
            'parent' => new ReplyResource($this->parent),
            'user' => new SimpleUserResource($this->user),
        ]);
    }
}
