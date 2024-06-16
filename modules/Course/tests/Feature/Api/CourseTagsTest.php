<?php

namespace Course\Feature\Api;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Taxonomy\Models\Tag;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CourseTagsTest extends TestCase
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
     * @group  feature:api
     * @group  feature:api:tags
     * @return void
     */
    public function a_user_can_store_tags_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.store', 'tags.store']), ['tags.store', 'courses.store']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->make();
        $tags = factory(Tag::class, 2)->make(['type' => $course->getTable()]);

        // Actions
        $attributes = array_merge($course->toArray(), ['tags' => $tags->toArray()]);
        $response = $this->post(route('api.courses.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $tags->each(function ($tag) {
            $this->assertDatabaseHas($tag->getTable(), $tag->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:tags
     * @return void
     */
    public function a_user_can_update_tags_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.update', 'tags.store', 'tags.update']), ['tags.store', 'tags.update', 'courses.update']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create(['user_id' => $user->getKey()]);
        $attributes = factory(Course::class)->make();
        $tags = factory(Tag::class, 2)->make(['type' => $course->getTable()]);

        // Actions
        $attributes = array_merge($attributes->toArray(), ['tags' => $tags->toArray()]);
        $response = $this->put(route('api.courses.update', $course->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $tags->each(function ($tag) {
            $this->assertDatabaseHas($tag->getTable(), $tag->toArray());
        });
    }
}
