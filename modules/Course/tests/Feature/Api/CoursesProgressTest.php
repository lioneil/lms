<?php

namespace Course\Feature\Api;

use Course\Enumerations\CourseDictionary;
use Course\Models\Course;
use Course\Models\Lesson;
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
class CoursesProgressTest extends TestCase
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
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_update_its_own_course_progress()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.progress']));
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $lessons = factory(Lesson::class, 3)->create(['sort' => 0, 'course_id' => $course->getKey()]);

        $course->publish();
        $course->subscribe($user);

        // Actions
        $attributes = [
            'user_id' => $user->getKey(),
            'status' => 'testing',
            'metadata' => [
                'current' => $lessons->get(1)->getKey(),
                'lessons' => [
                    ['id' => $lessons->get(0)->getKey(), 'locked' => false, 'status' => CourseDictionary::COMPLETED],
                    ['id' => $lessons->get(1)->getKey(), 'locked' => false, 'status' => CourseDictionary::COMPLETED],
                    ['id' => $lessons->get(2)->getKey(), 'locked' => true, 'status' => CourseDictionary::IN_PROGRESS],
                ],
            ],
        ];
        $response = $this->patch(route('api.courses.progress', $course->getKey()), $attributes);
        $attributes = [
            'user_id' => $user->getKey(),
            'status' => 'testing',
        ];

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas('progressions', $attributes);
    }
}
