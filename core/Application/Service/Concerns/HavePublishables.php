<?php

namespace Core\Application\Service\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

trait HavePublishables
{
    /**
     * Retrieve all published resources and
     * return a paginated list.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function listPublished()
    {
        $this->model = $this->published();

        if ($this->isSearching()) {
            $this->model = $this->whereIn(
                $this->getKeyName(), $this->search(
                    $this->request()->get('search')
                )->keys()->toArray()
            );
        }

        $model = $this->model->paginate($this->getPerPage());

        $sorted = $this->sortAndOrder($model);

        return new LengthAwarePaginator($sorted, $model->total(), $model->perPage());
    }

    /**
     * Update the publishing date to the current
     * timestamp and fire an event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function publish(Model $model): ?Model
    {
        return $model->publish();
    }

    /**
     * Update the publishing date to null and fire an event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function unpublish(Model $model): ?Model
    {
        return $model->unpublish();
    }

    /**
     * Update the drafted date to the current
     * timestamp and fire an event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function draft(Model $model): ?Model
    {
        return $model->draft();
    }

    /**
     * Update the expired date to the current
     * timestamp and fire an event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function expire(Model $model): ?Model
    {
        return $model->expire();
    }

    /**
     * Soft delete (if applicable) resources
     * that are expired.
     *
     * @return void
     */
    public function autoclean()
    {
        $this->where(
            $this->getExpiredAtKey(), '<', Carbon::now()
        )->get()->each->delete();
    }
}
