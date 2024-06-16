<?php

namespace Assessment\Http\Resources;

use Assessment\Http\Resources\FieldResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentResource extends JsonResource
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
            'user' => $this->user,
            'fields' => FieldResource::collection($this->fields),
        ]);
    }
}
