<?php

namespace Material\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
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
            'coursewareable' => $this->coursewareable,
            'created' => $this->created,
            'deleted' => $this->deleted,
            'icon' => $this->icon,
            'modified' => $this->modified,
            'url' => $this->url,
        ]);
    }
}
