<?php

namespace Widget\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use User\Http\Resources\RoleResource;
use Widget\Models\Widget;

class WidgetResource extends JsonResource
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
            'roles' => RoleResource::collection($this->roles),
            'created' => $this->created,
            'modified' => $this->modified,
        ]);
    }
}
