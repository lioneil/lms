<?php

namespace Course\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CoursewareResource extends JsonResource
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
            'icon' => $this->icon,
            'modified' => $this->modified,
            'url' => $this->url,
        ]);
    }
}
