<?php

namespace Core\Application\Breadcrumbs;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Breadcrumbs extends Collection implements BreadcrumbsInterface
{
    /**
     * The items array.
     *
     * @var array
     */
    protected $items;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The url middleware parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * The parameters from the
     * breadcrumb middleware.
     *
     * @var array
     */
    protected $crumbs;

    /**
     * Initialize the class with the manifest and request instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->parameters = $request->route()->parameters();
        $this->crumbs = $this->parseCrumbs($this->request->route()->middleware());
        $this->items = $this->parseSegments($this->request->route()->uri());
    }

    /**
     * Retrieve the request intance.
     *
     * @return \Illuminate\Http\Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Retrieve a segment instance from
     * array of segments.
     *
     * @param  integer $position
     * @return mixed
     */
    public function segment(int $position = 0)
    {
        return $this->items[$position] ?? null;
    }

    /**
     * Retrieve the parameter via key.
     *
     * @param  string $key
     * @return mixed
     */
    protected function parameter(string $key)
    {
        return $this->parameters[$key] ?? null;
    }

    /**
     * Retrieve the crumbs via key.
     *
     * @param  string $key
     * @return array
     */
    protected function crumbs(string $key)
    {
        if (is_null($this->crumbs)) {
            return $this->parseCrumbs($this->request->route()->middleware());
        }

        return $this->crumbs[$key];
    }

    /**
     * The parameters array.
     *
     * @return array
     */
    protected function parameters()
    {
        return $this->parameters;
    }

    /**
     * Parse the segments.
     *
     * @param  string $uri
     * @return \Illuminate\Support\Collection
     */
    protected function parseSegments(string $uri)
    {
        $keys = explode('/', $uri);
        $segments = array_combine($keys, $this->request()->segments());

        return Collection::make($segments)->mapWithKeys(function ($segment, $key) {
            return [
                $key => $this->parameter($this->cleanParam($key)) ?? $this->parseText($segment),
            ];
        })->values()->mapWithKeys(function ($segment, $key) {
            return [
                $key => [
                    'text' => $segment->{$this->crumbs('field')} ?? $segment ?? null,
                    'url' => $url = implode('/', array_slice($this->request()->segments(), 0, $key + 1)),
                ]
            ];
        });
    }

    /**
     * Retrieve the 'breadcrumb' middleware's parameter.
     *
     * @param  array $middlewares
     * @return array
     */
    protected function parseCrumbs(array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            if (strpos($middleware, 'breadcrumbs:') !== false) {
                $this->crumbs = explode(':', $middleware)[1] ?? null;
            }
        }

        $this->crumbs = explode(',', $this->crumbs);

        return count(array_filter($this->crumbs)) === count($this->crumbs)
             ? array_combine(['key', 'field'], $this->crumbs)
             : ['key' => null, 'field' => null];
    }

    /**
     * Parse the given segment with the
     * breadcrumb middleware.
     *
     * @param  string $segment
     * @return string
     */
    protected function parseText(string $segment)
    {
        return ucwords($segment);
    }

    /**
     * Transform the given key into a param.
     *
     * @param  mixed $key
     * @return string
     */
    protected function cleanParam($key)
    {
        $key = str_replace('{', '', $key);
        $key = str_replace('}', '', $key);

        return $key;
    }
}
