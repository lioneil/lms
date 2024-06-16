<?php

namespace Course\Http\Resources;

use Course\Models\Course;
use Illuminate\Http\Resources\Json\JsonResource;

class WebCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function toArray($request)
    {
        $data = collect(array_merge(parent::toArray($request), [
            'author' => $this->author,
            'overview' => $this->overview,
            'category' => $this->categorization,
            'created' => $this->created,
            'deleted' => $this->deleted,
            'modified' => $this->modified,
            'meta' => $this->meta,
        ]));

        if ($only = $request->get('only')) {
            $data = $data->only($only);
        }

        if ($request->has('playlist')) {
            $data['playlist'] = PlaylistResource::collection(
                $this->getPlaylistOfStudent($request->get('subscribed_by'))
            );
        } else {
            $data['contents'] = SimpleContentResource::collection($this->lessons);
        }

        if ($request->has('subscribed_by')) {
            $data['subscribed'] = $this->isSubscribedById($request->input('subscribed_by'));
        }

        return $data->toArray();
    }
}
