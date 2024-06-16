<?php

namespace Assessment\Feature\Api;

use Assessment\Models\Assessment;
use Assessment\Services\AssessmentServiceInterface;
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
 * @package Assessment\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PortAssessmentsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(AssessmentServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assessment
     * @return void
     */
    public function a_user_can_export_owned_assessments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assessments.export']));
        $this->withPermissionsPolicy();
        $assessment = factory(Assessment::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = [
            'id' => [$assessment->getKey()],
            'filename' => $filename = $this->faker->words($nb = 3, $asText = true).'.xlsx',
            'format' => $format = Excel::XLSX,
        ];
        $response = $this->post(route('api.assessments.export', $assessment->getKey()), $attributes);
        $header = $response->headers->get('content-disposition');

        // Assertions
        $response->assertSuccessful();
        $this->assertEquals($header, 'attachment; filename="'.$filename.'"');
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assessment
     * @return void
     */
    public function a_user_can_import_assessments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assessments.import']));
        $this->withPermissionsPolicy();
        ExcelFacade::fake();

        // Actions
        $attributes = ['file' => UploadedFile::fake()->create('assessments.xlsx')];
        $response = $this->put(route('api.assessments.import'), $attributes);
        $assessments = $this->service->all();

        // Assertions
        $response->assertSuccessful();
        $assessments->each(function ($assessment) {
            $this->assertDatabaseHas($assessment->getTable(), $assessment->toArray());
        });
    }
}
