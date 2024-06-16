<?php

namespace Tests\Course\Feature;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Feature
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SearchCoursesTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CourseServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.search
     * @group  searches.index
     * @return void
     */
    public function a_user_can_search_for_a_course()
    {
        // Arrangement
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.index']));
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 5)->create(['user_id' => $user->getKey()]);
        $course = factory(Course::class)->create(['user_id' => $user->getKey(), 'title' => $title = 'A New Search Course']);

        // Actions
        $response = $this->get(route('courses.index'), ['search' => $title]);

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('course::admin.index')
                 ->assertSeeText(trans('All Courses'))
                 ->assertSeeText($title);
    }
}
