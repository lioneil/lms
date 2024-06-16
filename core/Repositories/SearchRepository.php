<?php

namespace Core\Repositories;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SearchRepository extends ConfigRepository implements Contracts\SearchRepositoryInterface
{
    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Constructor to bind model to a repository.
     *
     * @param \Illuminate\Http\Request $request
     * @param array                    $items
     */
    public function __construct(Request $request, array $items = [])
    {
        $this->request = $request;
        $this->items = $items;
    }

    /**
     * Should perform search on all
     * searchable models.
     *
     * @param  string $query
     * @return mixed
     */
    public function search($query)
    {
        $this->items = collect($this->all())->mapWithKeys(function ($model) use ($query) {
            $model = new $model;
            $results = $model->search($query)
                ->get()->map(function ($model) {
                    return $model->toSearchableResultsArray();
                });

            return [$model->getTable() => $results];
        })->toArray();

        return $this;
    }
}
