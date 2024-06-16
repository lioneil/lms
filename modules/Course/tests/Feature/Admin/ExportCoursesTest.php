<?php

namespace Tests\Course\Feature\Admin;

use Course\Exports\CoursesExport;
use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Maatwebsite\Excel\Facades\Excel;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ExportCoursesTest extends TestCase
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
     *
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.favorite
     * @return void
     */
    public function a_super_user_can_export_courses_as_csv()
    {
        // Arrangements
        Excel::fake();
        $this->actingAs($user = $this->superAdmin);
        $courses = factory(Course::class, 5)->create();

        // Actions
        $attributes = ['filename' => 'courses.csv', 'format' => 'Csv'];
        $response = $this->post(route('courses.export'), $attributes);

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.export
     * @return void
     */
    public function guests_cannot_download_exported_courses()
    {
        // Arrangements
        $courses = factory(Course::class, 3)->create();

        // Actions
        $attributes = ['format' => 'pdf'];
        $response = $this->post(route('courses.export'), $attributes);

        // Assertions
        $response->assertRedirect(route('login'));
    }
}
