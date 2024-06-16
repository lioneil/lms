<?php

namespace Forum\Http\Resources;

use Comment\Http\Resources\CommentResource;
use Comment\Http\Resources\ResponseResource;
use Forum\Models\Thread;
use Illuminate\Http\Resources\Json\JsonResource;
use User\Http\Resources\User as UserResource;

class ThreadResource extends JsonResource
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
            'category' => $this->categorization,
            'created' => $this->created,
            'deleted' => $this->deleted,
            'modified' => $this->modified,
            'responses' => ResponseResource::collection($this->responses),
            'user' => new UserResource($this->user),
        ]));

        if ($request->has('comments')) {
            $data['comments'] = CommentResource::collection($this->comments);
        }

        if ($only = $request->get('only')) {
            $data = $data->only($only);
        }

        return $data->toArray();
    }
}
