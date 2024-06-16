<?php

namespace Course\Feature\Api;

use Course\Models\Course;
use Course\Models\Courseware;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CourseCoursewaresTest extends TestCase
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
     * @group  feature:api:coursewares
     * @return void
     */
    public function a_user_can_store_coursewares_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.store', 'coursewares.store']));
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->make(['user_id' => $user->getKey()]);
        $coursewares = factory(Courseware::class, 2)->make([
            'uri' => UploadedFile::fake()->create('test.pdf'),
            'coursewareable_id' => $course->getKey(),
            'coursewareable_type' => Course::class,
            'user_id' => $user->getKey(),
        ]);

        // Actions
        $attributes = array_merge($course->toArray(), ['coursewares' => $coursewares->toArray()]);
        $response = $this->post(route('api.courses.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $coursewares->each(function ($courseware) {
            $this->assertDatabaseHas(
                $courseware->getTable(),
                $courseware->only('title', 'user_id', 'coursewareable_type')
            );
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:coursewares
     * @return void
     */
    public function a_user_can_update_coursewares_from_database()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.update', 'coursewares.update']));
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create(['user_id' => $user->getKey()]);
        $courseware = factory(Courseware::class, 2)->create([
            'coursewareable_id' => $course->getKey(),
            'coursewareable_type' => Course::class,
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $coursewares = array_merge([$courseware->toArray()], factory(Courseware::class, 2)->make([
            'uri' => UploadedFile::fake()->create('test.pdf'),
            'coursewareable_id' => $course->getKey(),
            'coursewareable_type' => Course::class,
            'user_id' => $user->getKey(),
        ])->toArray());
        $coursewaresCount = count($coursewares);
        $attributes = array_merge($course->toArray(), ['coursewares' => $coursewares]);
        $response = $this->put(route('api.courses.update', $course->getKey()), $attributes);
        $coursewares = $this->service->find($course->getKey())->coursewares()->type('courseware')->get();

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($coursewares->count() == $coursewaresCount);
        $coursewares->each(function ($courseware) {
            $this->assertDatabaseHas($courseware->getTable(), $courseware->toArray());
        });
    }
}
