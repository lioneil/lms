<?php

namespace Assessment\Feature\Admin;

use Assessment\Models\Assessment;
use Assessment\Models\Field;
use Assessment\Services\FieldServiceInterface;
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
class ExportExamineesTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(FieldServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

     /**
     * @test
     * @group  feature
     * @group  feature:examinee
     * @group  examinees.export
     * @return void
     */
    public function a_super_user_can_export_examinees_as_csv()
    {
        // Arrangements
        Excel::fake();
        $this->actingAs($user = $this->superAdmin);
        $assessment = factory(Assessment::class)->create();
        $examinees = factory(Field::class, 5)->create(['form_id' => $assessment->getKey()]);

        // Actions
        $attributes = ['filename' => 'examinees.csv', 'format' => 'Csv'];
        $response = $this->post(route('examinees.export', $assessment->getKey()), $attributes);

        // Assertions
        // Add assertion for downloading the excel file.
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:examinee
     * @group  examinees.export
     * @return void
     */
    public function guests_cannot_download_exported_examinees()
    {
        // Arrangements
        $assessment = factory(Assessment::class)->create();
        $examinees = factory(Field::class, 5)->create(['form_id' => $assessment->getKey()]);
        // Actions
        $attributes = ['format' => 'pdf'];
        $response = $this->post(route('examinees.export', $assessment->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('login'));
    }
}
