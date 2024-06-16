<?php

namespace Course\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Taxonomy\Models\Category;

class CategoryResource extends JsonResource
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
            'author' => $this->author,
            'created' => $this->created,
            'modified' => $this->modified,
            'deleted' => $this->deleted,
        ]));

        if ($only = $request->get('only')) {
            $data = $data->only($only);
        }

        return $data->toArray();
    }
}
