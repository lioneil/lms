<?php

namespace User\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Detail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        unset($request);

        return [
            'key' => $this->key,
            'value' => $this->value,
            'icon' => $this->icon,
            'type' => $this->type,
        ];
    }
}
