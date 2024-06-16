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
class FieldsTest extends TestCase
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
     * @group  feature:api:fields
     * @return void
     */
    public function a_user_can_store_a_field_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['fields.store']), ['fields.store']);
        $this->withPermissionsPolicy();

        // Actions
        $field = factory(Field::class)->make();
        $attributes = ($field->toArray());
        $response = $this->post(route('api.fields.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('metadata')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:field
     * @return void
     */
    public function a_user_can_only_retrieve_an_owned_single_field()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['fields.show']), ['fields.show']);
        $this->withPermissionsPolicy();

        $assessment = factory(Assessment::class, 2)->create(['user_id' => $user->getKey()])->random();
        $field = factory(Field::class, 2)->create(['form_id' => $assessment->getKey()])->random();

        // Actions
        $response = $this->get(route('api.fields.show', $field->getKey()));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'title', 'code', 'type',
                    'metadata', 'form_id',
                ],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:field
     * @return void
     */
    public function a_user_can_only_update_an_owned_field()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['fields.update']), ['fields.update']);
        $this->withPermissionsPolicy();

        $assessment = factory(Assessment::class, 3)->create(['user_id' => $user->getKey()])->random();
        $field = factory(Field::class, 3)->create(['form_id' => $assessment->getKey()])->random();

        // Actions
        $attributes = factory(Field::class)->make()->toArray();

        $response = $this->put(route('api.fields.update', $field->getKey()), $attributes);
        $field = $this->service->find($field->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($field->getTable(), collect($attributes)->except('metadata')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:field
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_field()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['fields.destroy', 'fields.owned']), ['fields.destroy']);
        $this->withPermissionsPolicy();

        $assessment = factory(Assessment::class, 3)->create(['user_id' => $user->getKey()])->random();
        $field = factory(Field::class, 3)->create(['form_id' => $assessment->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.fields.destroy', $field->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($field->getTable(), $field->toArray());
    }
}
