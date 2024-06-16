<?php

namespace Course\Feature\Api;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
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
class PublishCoursesTest extends TestCase
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
     * @group  courses.publish
     * @group  user:courses.publish
     * @return void
     */
    public function a_user_can_publish_owned_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.publish']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->post(route('api.courses.publish', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($course->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.publish
     * @group  user:courses.publish
     * @return void
     */
    public function a_user_cannot_publish_others_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.publish']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->post(route('api.courses.publish', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($course->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.unpublish
     * @group  user:courses.unpublish
     * @return void
     */
    public function a_user_can_unpublish_a_published_owned_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.unpublish']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();
        $course->publish();

        // Actions
        $response = $this->post(route('api.courses.unpublish', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($course->isUnpublished());
        $this->assertFalse($course->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.unpublish
     * @group  user:courses.unpublish
     * @return void
     */
    public function a_user_cannot_unpublish_others_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.unpublish']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();
        $course->publish();

        // Actions
        $response = $this->post(route('api.courses.unpublish', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($course->isUnpublished());
        $this->assertTrue($course->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.draft
     * @group  user:courses.draft
     * @return void
     */
    public function a_user_can_draft_owned_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.draft']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->post(route('api.courses.draft', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($course->isPublished());
        $this->assertTrue($course->isDrafted());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.draft
     * @group  user:courses.draft
     * @return void
     */
    public function a_user_cannot_draft_others_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.draft']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 2)->create()->random();

        // Actions
        $response = $this->post(route('api.courses.draft', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($course->isDrafted());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.expire
     * @group  user:courses.expire
     * @return void
     */
    public function a_user_can_expire_owned_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.expire']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->patch(route('api.courses.expire', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($course->isExpired());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.expire
     * @group  user:courses.expire
     * @return void
     */
    public function a_user_cannot_expire_others_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.expire']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 2)->create()->random();

        // Actions
        $response = $this->patch(route('api.courses.expire', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($course->isExpired());
    }
}
