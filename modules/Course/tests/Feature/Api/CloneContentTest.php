<?php

namespace Course\Feature\Api;

use Course\Models\Content;
use Course\Models\Course;
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
class CloneContentTest extends TestCase
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
     * @group  feature:api
     * @group  feature:api:content:clone
     * @return void
     */
    public function a_user_can_clone_content()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.clone']), ['contents.clone']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create(['user_id' => $user->getKey()]);
        $content = factory(Content::class)->create(['course_id' => $course->getKey()]);

        // Actions
        $response = $this->post(route('api.contents.clone', $content->getKey()));
        $content = $response->original;
        $attributes = $content->only('title', 'subtitle', 'slug', 'course_id');

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'course_id' => $course->getKey(),
                 ]])
                 ->assertJsonStructure([
                    'data' => [
                        'title', 'subtitle', 'slug', 'description',
                        'content', 'sort', 'type', 'course_id',
                    ],
                 ]);
        $this->assertDatabaseHas($content->getTable(), $attributes);
    }
}
