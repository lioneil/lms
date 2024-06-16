<?php

namespace Announcement\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Taxonomy\Models\Category;
use User\Http\Resources\User as UserResource;

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

        if ($request->has('user') && $request->get('user')) {
            $data['user'] = new UserResource($this->user);
        }

        return $data->toArray();
    }
}
