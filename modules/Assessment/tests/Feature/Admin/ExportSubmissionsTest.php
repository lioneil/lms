<?php

namespace Assessment\Feature\Admin;

use Assessment\Models\Submission;
use Assessment\Services\SubmissionServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Maatwebsite\Excel\Facades\Excel;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Assessment\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ExportSubmissionsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(SubmissionServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

     /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.export
     * @return void
     */
    public function a_super_user_can_export_submissions_as_csv()
    {
        // Arrangements
        Excel::fake();
        $this->actingAs($user = $this->superAdmin);
        $submissions = factory(Submission::class, 5)->create();

        // Actions
        $attributes = ['filename' => 'submissions.csv', 'format' => 'Csv'];
        $response = $this->post(route('submissions.export'), $attributes);

        // Assertions
        // Add assertion for downloading the excel file.
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.export
     * @return void
     */
    public function guests_cannot_download_exported_submissions()
    {
        // Arrangements
        $submissions = factory(Submission::class, 3)->create();

        // Actions
        $attributes = ['format' => 'pdf'];
        $response = $this->post(route('submissions.export'), $attributes);

        // Assertions
        $response->assertRedirect(route('login'));
    }
}
