<?php

namespace Course\Feature\Api;

use Course\Enumerations\CourseDictionary;
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
class CompleteCourseContentTest extends TestCase
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
     * @group  feature:course
     * @group  feature:course:api
     * @return void
     */
    public function a_user_can_complete_a_course_content()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.complete']), ['contents.complete']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $contents = factory(Content::class, 4)->create([
            'course_id' => $course->getKey()
        ])->each->attachToSelf();

        $course->makeLockable();
        $course->publish();
        $course->subscribe($user);

        // Actions
        $content = $contents->first();
        $attributes = ['user_id' => $user->getKey()];

        $response = $this->patch(route('api.contents.complete', $content->getKey()), $attributes);

        $expected = [
            'id' => $content->getKey(),
            'locked' => false,
            'status' => CourseDictionary::COMPLETED,
            'type' => 'content',
        ];

        $actual = collect($course->progressionsOf($user)->first()->metadata['lessons']);
        $actual = $actual->where('id', $content->getKey())->first();

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual);
    }
}
