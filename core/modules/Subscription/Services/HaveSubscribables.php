<?php

namespace Subscription\Services;

use Illuminate\Pagination\LengthAwarePaginator;

trait HaveSubscribables
{
    /**
     * Retrieve all subscribed resources and
     * return a paginated list.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function listSubscribed()
    {
        $this->model = $this->subscribedBy($this->auth()->user());

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
}
