<?php

namespace Course\Feature\Api;

use Course\Models\Course;
use Course\Models\Lesson;
use Course\Services\ContentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class GetCourseContentTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ContentServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_cannot_visit_any_content_page_of_a_unpublished_course()
    {
        // Arrangements
        $course = factory(Course::class, 3)->create()->random();
        factory(Lesson::class, 3)->create(['course_id' => $course->getKey()]);

        $content = $course->playables->random();

        // Actions
        $response = $this->get(
            route('api.courses.content', [$course->getSlug(), $content->getSlug()])
        );

        // Assertions
        $response->assertNotFound();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_visit_any_content_page_of_a_published_course()
    {
        // Arrangements
        $course = factory(Course::class, 3)->create()->random();
        factory(Lesson::class, 3)->create(['course_id' => $course->getKey()]);

        $course->publish();
        $content = $course->playables->random();

        // Actions
        $response = $this->get(route('api.courses.content', [
            $course->getSlug(), $content->getSlug(), 'with' => ['course']
        ]));

        // Assertions
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'title', 'subtitle', 'slug',
                'description', 'content', 'sort',
                'type', 'author', 'created',
                'deleted', 'icon', 'imsmanifest',
                'is_scorm', 'is_section', 'modified',
                'scorm', 'course',
            ],
        ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_subscribed_user_can_visit_content_page_of_a_lockable_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.content']));
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 2)->create()->random();
        $content = factory(Lesson::class, 3)->create([
            'course_id' => $course->getKey()
        ])->random();

        $course->makeLockable();
        $course->publish();
        $course->subscribe($user);

        // Actions
        $response = $this->get(route('api.courses.content', [$course->getSlug(), $content->getSlug()]));

        // Assertions
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'title', 'subtitle', 'slug',
                'description', 'content', 'sort',
                'type', 'author', 'created',
                'deleted', 'icon', 'imsmanifest',
                'is_scorm', 'is_section', 'modified', 'scorm',
            ],
        ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function an_unsubscribed_user_can_visit_any_locked_content_page_of_a_lockable_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.content']));
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 2)->create()->random();
        $content = factory(Lesson::class, 3)->create([
            'course_id' => $course->getKey()
        ])->random();

        $course->makeLockable();
        $course->publish();

        // Actions
        $response = $this->get(route('api.courses.content', [$course->getSlug(), $content->getSlug()]));
        $content = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($content->isLocked());
    }
}
