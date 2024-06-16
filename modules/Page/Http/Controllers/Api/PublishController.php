<?php

namespace Page\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Page\Http\Requests\OwnedPageRequest;
use Page\Models\Page;
use Page\Services\PageServiceInterface;

class PublishController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Page\Services\PageServiceInterface $service
     */
    public function __construct(PageServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Publish the given resource.
     *
     * @param  \Page\Http\Requests\OwnedPageRequest $request
     * @param  \Page\Models\Page                    $page
     * @return \Illuminate\Http\Response
     */
    public function publish(OwnedPageRequest $request, Page $page)
    {
        return response()->json($this->service()->publish($page));
    }

    /**
     * Unpublish the given resource.
     *
     * @param  \Page\Http\Requests\OwnedPageRequest $request
     * @param  \Page\Models\Page                    $page
     * @return \Illuminate\Http\Response
     */
    public function unpublish(OwnedPageRequest $request, Page $page)
    {
        return response()->json($this->service()->unpublish($page));
    }

    /**
     * Draft the specified resource.
     *
     * @param  \Page\Http\Requests\OwnedPageRequest $request
     * @param  \Page\Models\Page                    $page
     * @return \Illuminate\Http\Response
     */
    public function draft(OwnedPageRequest $request, Page $page)
    {
        return response()->json($this->service()->draft($page));
    }

    /**
     * Expire the specified resource.
     *
     * @param  \Page\Http\Requests\OwnedPageRequest $request
     * @param  \Page\Models\Page                    $page
     * @return \Illuminate\Http\Response
     */
    public function expire(OwnedPageRequest $request, Page $page)
    {
        return response()->json($this->service()->expire($page));
    }
}
