<?php

namespace Classroom\Feature\Api;

use Classroom\Models\Classroom;
use Classroom\Services\ClassroomServiceInterface;
use Course\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Classroom\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DetachClassroomTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ClassroomServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom:detach
     * @return void
     */
    public function a_user_can_only_detach_classroom()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.detach']), ['classrooms.detach']);
        $this->withPermissionsPolicy();

        $classroom = factory(Classroom::class)->create(['user_id' => $user->getKey()]);
        $course = factory(Course::class)->create(['user_id' => $user->getKey()]);
        $this->service->attach($classroom, ['course_id' => $course->getKey()]);

        // Actions
        $attributes = ['course_id' => $course->getKey()];
        $response = $this->patch(route('api.classrooms.detach', $classroom->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($classroom->courses->contains($course));
    }
}
