<?php

namespace Assessment\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = collect(array_merge(parent::toArray($request), [
            'created' => $this->created,
            'deleted' => $this->deleted,
            'modified' => $this->modified,
            'excerpt' => $this->excerpt,
        ]));

        return $data->toArray();
    }
}
