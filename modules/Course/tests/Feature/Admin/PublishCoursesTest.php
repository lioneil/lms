<?php

namespace Tests\Course\Feature\Admin;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
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
     * @return void
     */
    public function a_super_user_can_publish_a_course()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->post(route('courses.publish', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertRedirect(route('courses.show', $course->getKey()));
        $this->assertDatabaseHas($course->getTable(), $course->toArray());
        $this->assertTrue($course->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.unpublish
     * @return void
     */
    public function a_super_user_can_unpublish_a_published_course()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class, 3)->create()->random();
        $course->publish();

        // Actions
        $response = $this->post(route('courses.unpublish', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertRedirect(route('courses.show', $course->getKey()));
        $this->assertDatabaseHas($course->getTable(), $course->toArray());
        $this->assertTrue($course->isUnpublished());
        $this->assertFalse($course->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.draft
     * @return void
     */
    public function a_super_user_can_draft_a_course()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->post(route('courses.draft', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertRedirect(route('courses.show', $course->getKey()));
        $this->assertDatabaseHas($course->getTable(), $course->toArray());
        $this->assertFalse($course->isPublished());
        $this->assertTrue($course->isDrafted());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.preview
     * @return void
     */
    public function a_super_user_can_only_preview_drafted_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();
        $course->draft();

        // Actions
        $response = $this->get(route('courses.preview', $course->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('course::admin.preview')
                 ->assertSeeText(trans('Preview Course'))
                 ->assertSeeTextInOrder([trans('Continue Editing'), trans('Move to Trash')])
                 ->assertSeeText($course->title)
                 ->assertSeeText($course->subtitle)
                 ->assertSeeTextInOrder($course->lessons->pluck('title')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.preview
     * @return void
     */
    public function a_super_user_can_preview_others_drafted_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 3)->create()->random();
        $course->draft();

        // Actions
        $response = $this->get(route('courses.preview', $course->getKey()));

        // Assertions
        $response->assertSuccessful();
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
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.publish']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->post(route('courses.publish', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertRedirect(route('courses.show', $course->getKey()));
        $this->assertDatabaseHas($course->getTable(), $course->toArray());
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
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.publish']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->post(route('courses.publish', $course->getKey()));
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
    public function a_user_can_unpublish_owned_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.unpublish']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();
        $course->publish();

        // Actions
        $response = $this->post(route('courses.unpublish', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertRedirect(route('courses.show', $course->getKey()));
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
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.unpublish']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();
        $course->publish();

        // Actions
        $response = $this->post(route('courses.unpublish', $course->getKey()));
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
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.draft']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->post(route('courses.draft', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertRedirect(route('courses.show', $course->getKey()));
        $this->assertDatabaseHas($course->getTable(), $course->toArray());
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
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.draft']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->post(route('courses.draft', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($course->isDrafted());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.preview
     * @return void
     */
    public function a_user_can_only_preview_owned_drafted_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.preview', 'courses.owned']));
        $this->actingAs($user = $this->asNonSuperAdmin([
            'courses.edit', 'courses.destroy', 'courses.preview', 'courses.owned'
        ]));
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();
        $course->draft();

        // Actions
        $response = $this->get(route('courses.preview', $course->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('course::admin.preview')
                 ->assertSeeText(trans('Preview Course'))
                 ->assertSeeTextInOrder([trans('Continue Editing'), trans('Move to Trash')])
                 ->assertSeeText($course->title)
                 ->assertSeeText($course->subtitle)
                 ->assertSeeTextInOrder($course->lessons->pluck('title')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.preview
     * @return void
     */
    public function a_user_cannot_preview_others_drafted_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.preview', 'courses.owned']));
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 3)->create()->random();
        $course->draft();

        // Actions
        $response = $this->get(route('courses.preview', $course->getKey()));

        // Assertions
        $response->assertForbidden();
    }
}
