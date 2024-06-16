<?php

namespace Assessment\Feature\Api;

use Assessment\Models\Assessment;
use Assessment\Services\AssessmentServiceInterface;
use Assignment\Services\AssignmentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Quiz\Models\Form;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Assessment\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AssessmentsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(AssessmentServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assessment
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_assessments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assessments.index']), ['assessments.index']);
        $this->withPermissionsPolicy();

        $assessment = factory(Assessment::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $response = $this->get(route('api.assessments.index'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'title', 'subtitle', 'description',
                    'slug', 'url', 'method',
                    'type', 'metadata', 'template_id',
                    'user_id', 'template', 'user','fields',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assessments
     * @return void
     */
    public function a_user_can_store_a_assessment_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assessments.store']), ['assessments.store']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Assessment::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('api.assessments.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('metadata')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assessment
     * @return void
     */
    public function a_user_can_only_retrieve_an_owned_single_assessment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assessments.show']), ['assessments.show']);
        $this->withPermissionsPolicy();

        $assessment = factory(Assessment::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('api.assessments.show', $assessment->getKey()));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'title','subtitle','description',
                    'slug','url','method','type',
                    'metadata','template_id','user_id',
                    'template','user','fields',
                ],
            ]);
    }

     /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assessment
     * @return void
     */
    public function a_user_can_only_update_an_owned_assessment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assessments.update']), ['assessments.update']);
        $this->withPermissionsPolicy();

        $assessment = factory(Assessment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = factory(Assessment::class)->make()->toArray();

        $response = $this->put(route('api.assessments.update', $assessment->getKey()), $attributes);
        $assessment = $this->service->find($assessment->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($assessment->getTable(), collect($attributes)->except('metadata')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assessment
     * @return void
     */
    public function a_user_can_only_soft_delete_an_owned_assessment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assessments.destroy']), ['assessments.destroy']);
        $this->withPermissionsPolicy();

        $assessment = factory(Assessment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.assessments.destroy', $assessment->getKey()));
        $assessment = $this->service->withTrashed()->find($assessment->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($assessment->getTable(), collect($assessment)->except('metadata')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assessment
     * @return void
     */
    public function a_user_can_only_soft_delete_multiple_owned_assessments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assessments.destroy']), ['assessments.destroy']);
        $this->withPermissionsPolicy();

        $assessments = factory(Assessment::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $assessments->pluck('id')->toArray()];
        $response = $this->delete(route('api.assessments.destroy', 'null'), $attributes);
        $assessments = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $assessments->each(function ($assessment) {
            $this->assertSoftDeleted($assessment->getTable(), collect($assessment)->except('metadata')->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assessment
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_trashed_assessments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assessments.trashed']), ['assessments.trashed']);
        $this->withPermissionsPolicy();

        $assessments = factory(Assessment::class, 2)->create(['user_id' => $user->getKey()]);
        $assessments->each->delete();

        // Actions
        $response = $this->get(route('api.assessments.trashed'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'title', 'subtitle', 'description',
                    'slug', 'url', 'method', 'type',
                    'metadata', 'template_id', 'user_id',
                    'template', 'user','fields',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assessment
     * @return void
     */
    public function a_user_can_only_restore_owned_destroyed_assessment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assessments.restore']), ['assessments.restore']);
        $this->withPermissionsPolicy();

        $assessment = factory(Assessment::class, 3)->create(['user_id' => $user->getKey()])->random();
        $assessment->delete();

        // Actions
        $response = $this->patch(route('api.assessments.restore', $assessment->getKey()));
        $assessment = $this->service->find($assessment->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($assessment->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assessments
     * @return void
     */
    public function a_user_can_only_restore_multiple_owned_destroyed_assessments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assessments.restore']), ['assessments.restore']);
        $this->withPermissionsPolicy();

        $assessments = factory(Assessment::class, 3)->create(['user_id' => $user->getKey()]);
        $assessments->each->delete();

        // Actions
        $attributes = ['id' => $assessments->pluck('id')->toArray()];
        $response = $this->patch(route('api.assessments.restore'), $attributes);
        $assessments = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $assessments->each(function ($assessment) {
            $this->assertFalse($assessment->trashed());
        });
    }

     /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assessment
     * @return void
     */
    public function a_user_can_only_permanently_delete_multiple_owned_assessments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assessments.delete']), ['assessments.delete']);
        $this->withPermissionsPolicy();

        $assessments = factory(Assessment::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $assessments->pluck('id')->toArray()];
        $response = $this->delete(route('api.assessments.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $assessments->each(function ($assessment) {
            $this->assertDatabaseMissing($assessment->getTable(), $assessment->toArray());
        });
    }
}
