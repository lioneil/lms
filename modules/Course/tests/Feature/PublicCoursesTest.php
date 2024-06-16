<?php

namespace Tests\Course\Feature;

use Course\Models\Course;
use Course\Models\Lesson;
use Course\Models\Tag;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Taxonomy\Models\Category;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Feature
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PublicCoursesTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CourseServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.all
     * @return void
     */
    public function guests_can_view_a_paginated_list_of_all_published_courses()
    {
        // Arrangements
        $courses = factory(Course::class, 20)->create();
        $courses->each->publish();

        // Actions
        $response = $this->get(route('courses.all'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('course::public.all')
                 ->assertSeeText(trans('All Courses'))
                 ->assertSeeTextInOrder($courses->take(15)->pluck('title')->toArray())
                 ->assertSeeTextInOrder($courses->take(15)->pluck('subtitle')->toArray())
                 ->assertSeeTextInOrder($courses->take(15)->pluck('excerpt')->toArray())
                 ->assertSeeTextInOrder($courses->take(15)->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder($courses->take(15)->map(function ($course) {
                    return sprintf(trans('%s Lessons'), $course->lessons->count());
                 })->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.all
     * @return void
     */
    public function guests_cannot_view_unpublished_courses()
    {
        // Arrangements
        $courses = factory(Course::class, 10)->create();
        $courses->each->unpublish();

        // Actions
        $response = $this->get(route('courses.all'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('course::public.all')
                 ->assertDontSeeText($courses->random()->title)
                 ->assertDontSeeText($courses->random()->subtitle)
                 ->assertDontSeeText($courses->random()->excerpt);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.all
     * @return void
     */
    public function logged_in_users_can_view_a_paginated_list_of_all_published_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->withPermissionsPolicy();

        $courses = factory(Course::class, 10)->create();
        $courses->each->publish();

        // Actions
        $response = $this->get(route('courses.all'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('course::public.all')
                 ->assertSeeText(trans('All Courses'))
                 ->assertSeeTextInOrder($courses->take(15)->pluck('title')->toArray())
                 ->assertSeeTextInOrder($courses->take(15)->pluck('subtitle')->toArray())
                 ->assertSeeTextInOrder($courses->take(15)->pluck('excerpt')->toArray())
                 ->assertSeeTextInOrder($courses->take(15)->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder($courses->take(15)->map(function ($course) {
                    return sprintf(trans('%s Lessons'), $course->lessons->count());
                 })->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.all
     * @return void
     */
    public function guests_can_view_a_single_published_course()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        $category = factory(Category::class)->create();
        $tags = factory(Tag::class, 2)->create();
        $course = factory(Course::class, 2)->create(['category_id' => $category->getKey()])->random();
        $course->tags()->sync($tags->pluck('id')->toArray());
        $lessons = factory(Lesson::class, 5)->create(['course_id' => $course->getKey(),]);
        $course->publish();

        // Actions
        $response = $this->get(route('courses.single', $course->slug));

        // Arrangements
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('course::public.single')
                 ->assertSeeText($course->title)
                 ->assertSeeText($course->subtitle)
                 ->assertSeeText($course->status)
                 ->assertSeeText($course->code)
                 ->assertSeeText(e($course->author))
                 ->assertSeeText($course->author)
                 ->assertSeeText($course->description)
                 ->assertSeeText($course->category->name)
                 ->assertSeeTextInOrder($course->tags->pluck('name')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('title')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('subtitle')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('slug')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('excerpt')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.all
     * @return void
     */
    public function logged_in_users_can_view_a_single_published_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->withPermissionsPolicy();
        $category = factory(Category::class)->create();
        $tags = factory(Tag::class, 2)->create();
        $course = factory(Course::class, 2)->create(['category_id' => $category->getKey()])->random();
        $course->tags()->sync($tags->pluck('id')->toArray());
        $lessons = factory(Lesson::class, 5)->create(['course_id' => $course->getKey(),]);
        $course->publish();

        // Actions
        $response = $this->get(route('courses.single', $course->slug));

        // Arrangements
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('course::public.single')
                 ->assertSeeText($course->title)
                 ->assertSeeText($course->subtitle)
                 ->assertSeeText($course->status)
                 ->assertSeeText($course->code)
                 ->assertSeeText(e($course->author))
                 ->assertSeeText($course->description)
                 ->assertSeeText($course->category->name)
                 ->assertSeeTextInOrder($course->tags->pluck('name')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('title')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('subtitle')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('slug')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('excerpt')->toArray());
    }
}
