<?php

namespace Classroom\Http\Resources;

use Classroom\Models\Classroom;
use Course\Http\Resources\CourseResource;
use Illuminate\Http\Resources\Json\JsonResource;
use User\Http\Resources\User as UserResource;

class ClassroomResource extends JsonResource
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
            'author' => $this->author,
            'courses' => CourseResource::collection($this->courses),
            'created' => $this->created,
            'deleted' => $this->deleted,
            'meta' => $this->meta,
            'modified' => $this->modified,
            'students' => UserResource::collection($this->students),
            'user' => $this->user,
        ]);
    }
}
