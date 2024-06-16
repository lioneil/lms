<?php

namespace Assessment\Feature\Api;

use Assessment\Models\Field;
use Assessment\Models\Submission;
use Assessment\Services\SubmissionServiceInterface;
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
class SubmissionsTest extends TestCase
{
    use ActingAsUser, RefreshDatabase, WithFaker, WithPermissionsPolicy;

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
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_submissions()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.index', 'submissions.owned']), ['submissions.index']);
        $this->withPermissionsPolicy();

        $submissions = factory(Submission::class, 2)->create(['user_id' => $user->getKey()]);

        // Actions
        $response = $this->get(route('api.submissions.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [[
                    'user_id' => $user->getKey(),
                ]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'user_id',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_trashed_submissions()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.trashed', 'submissions.owned']), ['submissions.trashed']);
        $this->withPermissionsPolicy();

        $submissions = factory(Submission::class, 5)->create(['user_id' => $user->getKey()]);
        $submissions->each->delete();

        // Actions
        $response = $this->get(route('api.submissions.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [[
                    'user_id' => $user->getKey(),
                ]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'user_id',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_can_visit_owned_submission_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.show', 'submissions.owned']), ['submissions.show']);
        $this->withPermissionsPolicy();

        $submission = factory(Submission::class, 5)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('api.submissions.show', $submission->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'user_id' => $user->getKey(),
                ]])
                 ->assertJsonStructure([
                    'data' => [
                        'user_id',
                    ],
                ]);
    }

     /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_can_visit_any_submission_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.show']), ['submissions.show']);
        $this->withPermissionsPolicy();

        $submission = factory(Submission::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('api.submissions.show', $submission->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'user_id' => $user->getKey(),
                ]])
                 ->assertJsonStructure([
                    'data' => [
                        'user_id',
                    ],
                ]);
    }

     /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_can_store_a_submission_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.store']), ['submissions.store']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Submission::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('api.submissions.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_can_only_update_their_owned_submissions()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.owned', 'submissions.update']), ['submissions.update']);
        $this->withPermissionsPolicy();

        $submission = factory(Submission::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = factory(Submission::class)->make()->toArray();
        $response = $this->put(route('api.submissions.update', $submission->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($submission->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_cannot_update_submissions_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.owned', 'submissions.update']), ['submissions.update']);
        $this->withPermissionsPolicy();
        $submission = factory(Submission::class, 3)->create()->random();

        // Actions
        $attributes = factory(Submission::class)->make()->toArray();
        $response = $this->put(route('api.submissions.update', $submission->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($submission->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_can_only_restore_owned_content()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.restore', 'submissions.owned']), ['submissions.restore']);
        $this->withPermissionsPolicy();
        $submission = factory(Submission::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->patch(route('api.submissions.restore', $submission->getKey()));
        $submission = $this->service->find($submission->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($submission->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_can_only_multiple_restore_owned_submissions()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.restore', 'submissions.owned']), ['submissions.restore']);
        $this->withPermissionsPolicy();
        $submissions = factory(Submission::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->patch(route('api.submissions.restore'), $attributes);
        $submissions = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $submissions->each(function ($submission) {
            $this->assertFalse($submission->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_cannot_restore_submission_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['submissions.restore', 'submissions.owned']), ['submissions.restore']);
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['submissions.owned', 'submissions.restore']);
        $submission = factory(Submission::class, 3)->create(['user_id' => $otherUser->getKey()])->random();

        // Actions
        $response = $this->patch(route('api.submissions.restore', $submission->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($submission->getTable(), $submission->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_cannot_multiple_restore_submissions_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['submissions.restore', 'submissions.owned']), ['submissions.restore']);
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['submissions.owned', 'submissions.restore']);
        $submissions = factory(Submission::class, 3)->create(['user_id' => $otherUser->getKey()]);

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->patch(route('api.submissions.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
        $submissions->each(function ($submission) {
            $this->assertDatabaseHas($submission->getTable(), $submission->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_submission()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.destroy', 'submissions.owned']), ['submissions.destroy']);
        $this->withPermissionsPolicy();
        $submission = factory(Submission::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.submissions.destroy', $submission->getKey()));
        $submission = $this->service->withTrashed()->find($submission->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($submission->getTable(), $submission->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_submissions()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.destroy', 'submissions.owned']), ['submissions.destroy']);
        $this->withPermissionsPolicy();
        $submissions = factory(Submission::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->delete(route('api.submissions.destroy', 'null'), $attributes);
        $submissions = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $submissions->each(function ($submission) {
            $this->assertSoftDeleted($submission->getTable(), $submission->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_cannot_soft_delete_submission_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['submissions.destroy', 'submissions.owned']), ['submissions.destroy']);
        $this->withPermissionsPolicy();
        $submission = factory(Submission::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.submissions.destroy', $submission->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($submission->getTable(), $submission->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_cannot_multiple_soft_delete_submissions_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['submissions.destroy', 'submissions.owned']), ['submissions.destroy']);
        $this->withPermissionsPolicy();
        $submissions = factory(Submission::class, 3)->create();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->delete(route('api.submissions.destroy', 'null'), $attributes);

        // Assertions
        $response->assertForbidden();
        $submissions->each(function ($submission) {
            $this->assertDatabaseHas($submission->getTable(), $submission->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_submission()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.delete', 'submissions.owned']), ['submissions.delete']);
        $this->withPermissionsPolicy();
        $submission = factory(Submission::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.submissions.delete', $submission->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($submission->getTable(), $submission->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_submissions()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['submissions.delete', 'submissions.owned']), ['submissions.delete']);
        $this->withPermissionsPolicy();
        $submissions = factory(Submission::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->delete(route('api.submissions.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $submissions->each(function ($submission) {
            $this->assertDatabaseMissing($submission->getTable(), $submission->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_cannot_permanently_delete_submission_owned_by_other_submissions()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['submissions.delete', 'submissions.owned']), ['submissions.delete']);
        $this->withPermissionsPolicy();
        $submission = factory(Submission::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.submissions.delete', $submission->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($submission->getTable(), $submission->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:submission
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_submissions_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['submissions.delete', 'submissions.owned']), ['submissions.delete']);
        $this->withPermissionsPolicy();
        $submissions = factory(Submission::class, 3)->create();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->delete(route('api.submissions.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
        $submissions->each(function ($submission) {
            $this->assertDatabaseHas($submission->getTable(), $submission->toArray());
        });
    }
}
