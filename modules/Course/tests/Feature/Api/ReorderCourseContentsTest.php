<?php

namespace Tests\Course\Feature\Api;

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
class ReorderCourseContentsTest extends TestCase
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
     * @group  feature:api:content:reorder
     * @return void
     */
    public function a_user_can_reorder_contents()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.reorder']), ['contents.reorder']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $contents = factory(Content::class, 4)->create([
            'sort' => 0,
            'course_id' => $course->getKey(),
        ])->map->only('id', 'sort');

        // Actions
        $attributes = $contents->map(function ($content) {
            return array_merge($content, [
                'sort' => $this->faker->randomDigitNotNull(),
            ]);
        });

        $attributes = ['contents' => $attributes->toArray()];
        $response = $this->patch(route('api.contents.reorder', $attributes));
        $content = $this->service->find($contents->random()['id']);

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($content->sort == 0);
    }
}
