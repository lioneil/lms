<?php

namespace User\Services\Concerns;

use User\Events\UserUpdating;

trait SavesAccountRecord
{
    /**
     * Create model resource.
     *
     * @param  array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $attributes)
    {
        $model = $this->model;
        $user = $this->save($model, $attributes);
        $user->record($attributes);

        return $user;
    }

    /**
     * Update model resource.
     *
     * @param  integer $id
     * @param  array   $attributes
     * @return boolean
     */
    public function update(int $id, array $attributes): bool
    {
        $model = $this->model->findOrFail($id);
        $model = $this->save($model, $attributes);
        $model->record($attributes);

        return $model->exists();
    }
}
