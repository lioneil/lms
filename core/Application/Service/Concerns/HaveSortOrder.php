<?php

namespace Core\Application\Service\Concerns;

use Illuminate\Support\Facades\Schema;

trait HaveSortOrder
{
    /**
     * Sort and order based on the url parameters.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function sortAndOrder($model = null)
    {
        if ($this->isFilteredOrderKeyAscending()) {
            $sorted = $model->sortBy($this->getFilteredSortKey());
        } else {
            $sorted = $model->sortByDesc($this->getFilteredSortKey());
        }

        return $sorted;
    }

    /**
     * Retrieve the sort key from url parameter
     * and check if it exists as attribute or column.
     *
     * @param  string|null $default
     * @return string
     */
    protected function getFilteredSortKey($default = 'id'): string
    {
        return $this->request()->get('sort') ?? $default;
    }

    /**
     * Retrieve the order key from url.
     *
     * @return string
     */
    protected function getFilteredOrderKey(): string
    {
        return $this->request()->get('order') ?? 'asc';
    }

    /**
     * Check if the filted sort key is ascending.
     *
     * @return boolean
     */
    protected function isFilteredOrderKeyAscending(): bool
    {
        return $this->getFilteredOrderKey() == 'asc';
    }
}
