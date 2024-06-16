<?php

namespace Core\Application\Service\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;

trait HaveSoftDeletes
{
    /**
     * Include only soft deleted records in the results.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function listTrashed()
    {
        if ($this->isSearching()) {
            $this->model = $this->searchTrash();
        }

        $this->model = $this->with($this->appendToList ?? [])->onlyTrashed();

        $model = $this->onlyOwned();

        $model = $model->paginate($this->getPerPage());

        $sorted = $this->sortAndOrder($model);

        return new LengthAwarePaginator($sorted, $model->total(), $model->perPage());
    }

    /**
     * Search through the search index,
     * then filter to retrieve only trashed resources.
     *
     * @param  string $keyword
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function searchTrash($keyword = null)
    {
        $ids = with($this->search($keyword)->raw(), function ($raw) {
            return $raw['ids'] ?? [];
        });

        return $this->model->whereIn($this->getScoutKeyName(), $ids)->onlyTrashed();
    }
}
