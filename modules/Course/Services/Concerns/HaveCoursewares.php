<?php

namespace Course\Services\Concerns;

use Course\Models\Courseware;
use Illuminate\Database\Eloquent\Model;

trait HaveCoursewares
{
    /**
     * Update or create the array of coursewares.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  array                               $coursewares
     * @return void
     */
    public function handleCoursewares(Model $model, array $coursewares)
    {
        $id = collect($coursewares)->reject(function ($courseware) {
            return ! isset($courseware['id']);
        })->map(function ($courseware) {
            return $courseware['id'];
        })->toArray();

        $model->coursewares()->whereNotIn('id', $id)->get()->each(function ($courseware) {
            $this->deleteFileFromStorage($courseware->pathname);
            $courseware->forceDelete();
        });

        foreach ($coursewares as $courseware) {
            $model->coursewares()->updateOrCreate([
                'id' => $courseware['id'] ?? null,
            ], $this->getSanitizedCourseware($model, $courseware));
        }
    }

    /**
     * Retrieve or create new coursewares.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  array                               $courseware
     * @return \Illuminate\Support\Collection
     */
    public function getSanitizedCourseware(Model $model, array $courseware)
    {
        return [
            'title' => $courseware['title'],
            'uri' => $this->handleCoursewareUpload($courseware),
            'pathname' => $this->getPathname() ?? $courseware['pathname'],
            'type' => $courseware['type'],
            'user_id' => $courseware['user_id'] ?? $model->user->getKey(),
        ];
    }

    /**
     * Retrieve or upload a file.
     *
     * @see    \Core\Application\Service\Concerns\CanUploadFile@upload
     * @param  array $attributes
     * @return string
     */
    public function handleCoursewareUpload($attributes)
    {
        return is_file($attributes['uri'] ?? false)
            ? $this->upload($attributes['uri'])
            : $attributes['uri_old'] ?? $attributes['uri'] ?? null;
    }
}
