<?php

namespace Course\Feature\Api;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PortCoursesTest extends TestCase
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
     * @group  feature:course
     * @return void
     */
    public function a_user_can_export_owned_courses()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.export']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = [
            'id' => [$course->getKey()],
            'filename' => $filename = $this->faker->words($nb = 3, $asText = true).'.xlsx',
            'format' => $format = Excel::XLSX,
        ];
        $response = $this->post(route('api.courses.export', $course->getKey()), $attributes);
        $header = $response->headers->get('content-disposition');

        // Assertions
        $response->assertSuccessful();
        $this->assertEquals($header, 'attachment; filename="'.$filename.'"');
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @return void
     */
    public function a_user_can_import_courses()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.import']));
        $this->withPermissionsPolicy();
        ExcelFacade::fake();

        // Actions
        $attributes = ['file' => UploadedFile::fake()->create('courses.xlsx')];
        $response = $this->put(route('api.courses.import'), $attributes);
        $courses = $this->service->all();

        // Assertions
        $response->assertSuccessful();
        $courses->each(function ($course) {
            $this->assertDatabaseHas($course->getTable(), $course->toArray());
        });
    }
}
