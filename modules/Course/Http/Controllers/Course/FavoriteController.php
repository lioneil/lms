<?php

namespace Course\Http\Controllers\Course;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Favorite\Http\Controllers\FavoriteController as AdminController;
use Illuminate\Http\Request;

class FavoriteController extends AdminController
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
    public function favorites(Request $request)
    {
        $resources = $this->service()->listFavorites();

        return view('course::admin.favorites')->withResources($resources);
    }

    /**
     * Favorite the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Course\Models\Course    $course
     * @return \Illuminate\Http\Response
     */
    public function favorite(Request $request, Course $course)
    {
        $this->service()->findOrFail($course->getKey())->favorite();

        return back();
    }

    /**
     * Unfavorite the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Course\Models\Course    $course
     * @return \Illuminate\Http\Response
     */
    public function unfavorite(Request $request, Course $course)
    {
        $this->service()->findOrFail($course->getKey())->unfavorite();

        return back();
    }
}
