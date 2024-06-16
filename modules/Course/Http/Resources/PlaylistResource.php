<?php

namespace Course\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaylistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = array_merge(parent::toArray($request), [
            'created' => $this->created,
            'modified' => $this->modified,
            'deleted' => $this->deleted,
            'icon' => $this->icon,
            'has_child' => $this->hasChildren(),
            'is_section' => $this->isSection(),
            'is_scorm' => $this->isScorm(),
            'is_locked' => $this->isLocked($request->get('subscribed_by')),
            'imsmanifest' => $this->imsmanifest,
            'scorm' => $this->scorm,
        ]);

        $data['is_completed'] = $this->withProgressOf($request->get('subscribed_by'))->isCompleted();

        $data['children'] = PlaylistResource::collection(
            $this->getChildrenWithProgressOf($request->get('subscribed_by'))->sortBy('sort')
        );

        return $data;
    }
}
