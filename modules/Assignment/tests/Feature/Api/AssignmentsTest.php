<?php

namespace Assignment\Feature\Api;

use Assignment\Models\Assignment;
use Assignment\Services\AssignmentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Assignment\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AssignmentsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(AssignmentServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_super_user_can_only_view_their_owned_paginated_list_of_assignments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.index', 'assignments.owned']), ['assignments.index']);
        $this->withPermissionsPolicy();

        $assignments = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('api.assignments.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [['user_id' => $user->getKey()]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'title',
                        'user_id',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_trashed_assignments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.trashed', 'assignments.owned']), ['assignments.trashed']);
        $this->withPermissionsPolicy();

        $assignments = factory(Assignment::class, 2)->create(['user_id' => $user->getKey()]);
        $assignments->each->delete();

        // Actions
        $response = $this->get(route('api.assignments.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [['user_id' => $user->getKey()]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'title',
                        'user_id',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_can_visit_their_owned_assignment_assignment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.show', 'assignments.owned']), ['assignments.show']);
        $this->withPermissionsPolicy();

        $assignment = factory(Assignment::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('api.assignments.show', $assignment->getKey()));

        // Assertions
        $response->assertSuccessful()
                ->assertJson(['data' => ['user_id' => $user->getKey()]])
                ->assertJsonStructure([
                    'data' => [
                        'title',
                        'user_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_can_visit_any_assignment_assignment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.show']), ['assignments.show']);
        $this->withPermissionsPolicy();

        $assignment = factory(Assignment::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('api.assignments.show', $assignment->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => ['user_id' => $user->getKey()]])
                 ->assertJsonStructure([
                    'data' => [
                        'title',
                        'user_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_can_store_a_assignment_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.store']), ['assignments.store']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Assignment::class)->make(['user_id' => $user->getKey()])->toArray();
        $attributes['file'] = UploadedFile::fake()->create('tmp.text');
        $response = $this->post(route('api.assignments.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('file', 'uri', 'pathname')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_can_only_update_their_owned_assignments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.owned', 'assignments.update']), ['assignments.update']);
        $this->withPermissionsPolicy();

        $assignment = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = factory(Assignment::class)->make()->toArray();
        $attributes['file'] = UploadedFile::fake()->create('tmp.text');
        $response = $this->put(route('api.assignments.update', $assignment->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('file', 'uri', 'pathname')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_cannot_update_assignments_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.owned', 'assignments.update']), ['assignments.update']);
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 3)->create()->random();

        // Actions
        $attributes = factory(Assignment::class)->make()->toArray();
        $response = $this->put(route('api.assignments.update', $assignment->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($assignment->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_can_only_restore_owned_assignment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.restore', 'assignments.owned']), ['assignments.restore']);
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->patch(route('api.assignments.restore', $assignment->getKey()));
        $assignment = $this->service->find($assignment->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($assignment->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_can_only_multiple_restore_owned_assignments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.restore', 'assignments.owned']), ['assignments.restore']);
        $this->withPermissionsPolicy();
        $assignments = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->patch(route('api.assignments.restore'), $attributes);
        $assignments = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $assignments->each(function ($assignment) {
            $this->assertFalse($assignment->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_cannot_restore_assignment_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['assignments.restore', 'assignments.owned']), ['assignments.restore']);
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['assignments.owned', 'assignments.restore']);
        $assignment = factory(Assignment::class, 3)->create(['user_id' => $otherUser->getKey()])->random();

        // Actions
        $response = $this->patch(route('api.assignments.restore', $assignment->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($assignment->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_cannot_multiple_restore_assignments_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['assignments.restore', 'assignments.owned']), ['assignments.restore']);
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['assignments.owned', 'assignments.restore']);
        $assignments = factory(Assignment::class, 3)->create(['user_id' => $otherUser->getKey()]);

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->patch(route('api.assignments.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
        $assignments->each(function ($assignment) {
            $this->assertDatabaseHas($assignment->getTable(), $assignment->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_assignment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.destroy', 'assignments.owned']), ['assignments.destroy']);
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.assignments.destroy', $assignment->getKey()));
        $assignment = $this->service->withTrashed()->find($assignment->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($assignment->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_assignments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.destroy', 'assignments.owned']), ['assignments.destroy']);
        $this->withPermissionsPolicy();
        $assignments = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->delete(route('api.assignments.destroy', 'null'), $attributes);
        $assignments = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $assignments->each(function ($assignment) {
            $this->assertSoftDeleted($assignment->getTable(), $assignment->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_cannot_soft_delete_assignment_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['assignments.destroy', 'assignments.owned']), ['assignments.destroy']);
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.assignments.destroy', $assignment->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($assignment->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_cannot_multiple_soft_delete_assignments_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['assignments.destroy', 'assignments.owned']), ['assignments.destroy']);
        $this->withPermissionsPolicy();
        $assignments = factory(Assignment::class, 3)->create();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->delete(route('api.assignments.destroy', 'null'), $attributes);

        // Assertions
        $response->assertForbidden();
        $assignments->each(function ($assignment) {
            $this->assertDatabaseHas($assignment->getTable(), $assignment->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_assignment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.delete', 'assignments.owned']), ['assignments.delete']);
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.assignments.delete', $assignment->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($assignment->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_assignments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['assignments.delete', 'assignments.owned']), ['assignments.delete']);
        $this->withPermissionsPolicy();
        $assignments = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->delete(route('api.assignments.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $assignments->each(function ($assignment) {
            $this->assertDatabaseMissing($assignment->getTable(), $assignment->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_cannot_permanently_delete_assignment_owned_by_other_assignments()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['assignments.delete', 'assignments.owned']), ['assignments.delete']);
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.assignments.delete', $assignment->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($assignment->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:assignment
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_assignments_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['assignments.delete', 'assignments.owned']), ['assignments.delete']);
        $this->withPermissionsPolicy();
        $assignments = factory(Assignment::class, 3)->create();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->delete(route('api.assignments.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
        $assignments->each(function ($assignment) {
            $this->assertDatabaseHas($assignment->getTable(), $assignment->toArray());
        });
    }
}
