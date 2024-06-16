<?php

namespace Course\Http\Resources;

use Comment\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
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
            'deleted' => $this->deleted,
            'icon' => $this->icon,
            'imsmanifest' => $this->imsmanifest,
            'is_locked' => $this->isLocked($request->get('subscribed_by')),
            'is_scorm' => $this->isScorm(),
            'is_section' => $this->isSection(),
            'modified' => $this->modified,
            'playable' => $this->isPlayable(),
            'scorm' => $this->scorm,
        ]));

        if ($only = $request->get('only')) {
            $data = $data->only($only);
        }

        foreach ((array) ($request->get('with') ?? []) as $key) {
            switch ($key) {
                case 'playlist':
                    $data['playlist'] = PlaylistResource::collection($this->course->playlist);
                    break;

                case 'course':
                    $data['course'] = new CourseResource($this->course);
                    break;

                case 'comments':
                    $data['comments'] = CommentResource::collection($this->comments);
                    break;

                default:
                    $data[$key] = $this->$key;
                    break;
            }
        }

        return $data->toArray();
    }
}
