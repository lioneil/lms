<?php

namespace Assessment\Feature\Api;

use Assessment\Models\Assessment;
use Assessment\Models\Field;
use Assessment\Services\FieldServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Assessment\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CloneFieldsTest extends TestCase
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
     * @group  feature:api
     * @group  feature:api:field:clone
     * @return void
     */
    public function a_user_can_clone_field()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['fields.clone']), ['fields.clone']);
        $this->withPermissionsPolicy();

        $assessment = factory(Assessment::class)->create(['user_id' => $user->getKey()]);
        $field = factory(Field::class)->create(['form_id' => $assessment->getKey()]);

        // Actions
        $response = $this->post(route('api.fields.clone', $field->getKey()));
        $field = $response->original;
        $attributes = $field->only('title', 'code', 'type', 'form_id');

        // Assertions
        $response->assertSuccessful()
                 ->assertJsonStructure([
                    'data' => [
                        'title', 'code', 'type', 'metadata',
                        'form_id', 'group', 'sort',
                    ],
                 ]);
        $this->assertDatabaseHas($field->getTable(), $attributes);
    }
}
