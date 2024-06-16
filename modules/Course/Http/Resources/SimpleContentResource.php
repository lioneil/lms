<?php

namespace Course\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleContentResource extends JsonResource
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
            'created' => $this->created,
            'modified' => $this->modified,
            'deleted' => $this->deleted,
            'icon' => $this->icon,
            'is_section' => $this->isSection(),
            'is_scorm' => $this->isScorm(),
            'imsmanifest' => $this->imsmanifest,
            'scorm' => $this->scorm,
        ]);
    }
}
