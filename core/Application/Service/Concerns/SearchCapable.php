<?php

namespace Core\Application\Service\Concerns;

trait SearchCapable
{
    /**
     * Search the resources using the
     * provided search driver.
     *
     * @param  string $keyword
     * @return \Core\Application\Repository\Repository
     */
    public function search($keyword = null)
    {
        return $this->model()->search($keyword ?? $this->request()->get('search'));
    }

    /**
     * Check if request parameter has search key.
     *
     * @return boolean
     */
    protected function isSearching()
    {
        return ! is_null($this->request()->get('search'));
    }
}
