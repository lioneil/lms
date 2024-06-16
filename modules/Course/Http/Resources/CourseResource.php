<?php

namespace Course\Http\Resources;

use Course\Http\Resources\CoursewareResource;
use Course\Models\Course;
use Illuminate\Http\Resources\Json\JsonResource;
use Material\Http\Resources\MaterialResource;

class CourseResource extends JsonResource
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
            'tags' => $this->getTagsAsArray(),
            'meta' => $this->meta,
            'published' => $this->isPublished(),
            'unpublished' => $this->isUnpublished(),
        ]));

        if ($request->has('playlist')) {
            $data['playlist'] = PlaylistResource::collection(
                $this->getPlaylistOfStudent($request->get('subscribed_by'))
            );
        } else {
            $data['contents'] = SimpleContentResource::collection($this->lessons);
        }

        if ($request->has('materials')) {
            $data['materials'] = MaterialResource::collection($this->materials);
        }

        if ($request->has('coursewares')) {
            $data['coursewares'] = CoursewareResource::collection($this->coursewares);
        }

        if ($request->has('subscribed_by')) {
            $data['subscribed'] = $this->isSubscribedById($request->input('subscribed_by'));
        }

        if ($only = $request->has('only')) {
            $data = $data->only($only);
        }

        return $data->toArray();
    }
}
