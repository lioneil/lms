<?php

namespace Course\Http\Controllers\Course;

use Core\Http\Controllers\AdminController;
use Course\Http\Requests\PublishedRequest;
use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Http\Request;

class SubscriptionController extends AdminController
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
        $resources = $this->service()->listSubscribed();

        return view('course::public.subscriptions')->withResources($resources);
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
        $this->service()->findOrFail($course->getKey())->subscribe();

        return redirect()->route('courses.show', $course->getKey());
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
        $this->service()->findOrFail($course->getKey())->unsubscribe();

        return back();
    }
}
