<?php

namespace Assignment\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Assignment\Models\Assignment;

class AssignmentResource extends JsonResource
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
            'user' => $this->user,
            'coursewareable' => $this->coursewareable,
            'modified' => $this->modified,
            'created' => $this->created,
            'deleted' => $this->deleted,
        ]));

        if ($only = $request->get('only')) {
           $data = $data->only($only);
        }

        return $data->toArray();
    }

}
