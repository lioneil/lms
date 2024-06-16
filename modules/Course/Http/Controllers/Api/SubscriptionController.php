<?php

namespace Course\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Course\Http\Requests\PublishedRequest;
use Course\Http\Resources\CourseResource;
use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Http\Request;

class SubscriptionController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Course\Services\CourseServiceInterface $service
     */
    public function __construct(CourseServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Display list of favorite resources.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function subscriptions(Request $request)
    {
        return CourseResource::collection($this->service()->listSubscribed());
    }

    /**
     * Subscribe the logged in user to the resource.
     *
     * @param  \Course\Http\Requests\PublishedRequest $request
     * @param  \Course\Models\Course                  $course
     * @return \Illuminate\Http\Response
     */
    public function subscribe(PublishedRequest $request, Course $course)
    {
        return response()->json(
            $this->service()->findOrFail($course->getKey())->subscribe()
        );
    }

    /**
     * Unsubscribe the logged in user to the resource.
     *
     * @param  \Course\Http\Requests\PublishedRequest $request
     * @param  \Course\Models\Course                  $course
     * @return \Illuminate\Http\Response
     */
    public function unsubscribe(PublishedRequest $request, Course $course)
    {
        return response()->json(
            $this->service()->findOrFail($course->getKey())->unsubscribe()
        );
    }
}
