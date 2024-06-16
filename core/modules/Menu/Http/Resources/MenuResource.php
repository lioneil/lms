<?php

namespace Menu\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Menu\Models\Menu;

class MenuResource extends JsonResource
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
            'url' => $this->url,
            'location' => $this->location,
            'created' => $this->created,
            'modified' => $this->modified,
        ]);
    }
}
