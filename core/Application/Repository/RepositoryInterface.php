<?php

namespace Core\Application\Repository;

interface RepositoryInterface
{
    /**
     * Retrieve all model resources as array.
     *
     * @return object
     */
    public function all();

    /**
     * Retrieve model collection.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get();

    /**
     * Retrieve model resource details.
     *
     * @param  integer $id
     * @return \Core\Models\Model
     */
    public function find(int $id);

    /**
     * Create model resource.
     *
     * @param  array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $attributes);

    /**
     * Update model resource.
     *
     * @param  integer $id
     * @param  array   $attributes
     * @return boolean
     */
    public function update(int $id, array $attributes);

    /**
     * Permanently delete model resource.
     *
     * @param  integer|array $id
     * @return boolean
     */
    public function delete($id);

    /**
     * Soft delete model resource.
     *
     * @param  integer|array $id
     * @return boolean
     */
    public function destroy($id);

    /**
     * Restore model resource.
     *
     * @param  integer|array $id
     * @return boolean
     */
    public function restore($id);
}
