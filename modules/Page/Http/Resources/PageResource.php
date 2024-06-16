<?php

namespace Page\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Page\Models\Page;
use User\Http\Resources\User as UserResource;

class PageResource extends JsonResource
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
            'templatepath' => $this->templatepath,
            'published' => $this->isPublished(),
            'unpublished' => $this->isUnpublished(),
            'user' => new UserResource($this->user),
            'status' => $this->status,
        ]));

        if ($only = $request->get('only')) {
            $data = $data->only($only);
        }

        return $data->toArray();
    }
}
