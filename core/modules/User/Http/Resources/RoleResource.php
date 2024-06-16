<?php

namespace User\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'created' => $this->created,
            'deleted' => $this->deleted,
            'modified' => $this->modified,
            'status' => $this->status,
            'permissions' => PermissionResource::collection($this->permissions),
            'permissions:selected' => $this->permissions->pluck('id')->toArray(),
        ]));

        if ($only = $request->get('only')) {
            $data = $data->only($only);
        }

        return $data->toArray();
    }
}
