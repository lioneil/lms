<?php

namespace Announcement\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use User\Http\Resources\User as UserResource;

class AnnouncementResource extends JsonResource
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
            'template' => $this->template,
            'user' => new UserResource($this->user),
            'user' => $this->user,
            'is_published' => $this->isPublished(),
            'is_expired' => $this->isExpired(),
        ]);
    }
}
