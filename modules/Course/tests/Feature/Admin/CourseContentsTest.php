<?php

namespace Course\Feature\Admin;

use Course\Models\Content;
use Course\Models\Course;
use Course\Services\ContentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CourseContentsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ContentServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.content
     * @group  user:courses.content
     * @return void
     */
    public function a_user_can_visit_course_contents()
    {
        // Arrangement
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class)->create();
        $contents = factory(Content::class, 2)->create([
            'course_id' => $course->getKey(),
            'content' => $this->faker->imageUrl(),
        ]);

        // Actions
        $response = $this->get(route('courses.content', [$course->slug, $contents->random()->slug]));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('course::public.content')
                 ->assertSeeText(trans('In this Course'))
                 ->assertSeeText(trans('About this Episode'))
                 ->assertSeeText(trans('Published on'));
    }
}
