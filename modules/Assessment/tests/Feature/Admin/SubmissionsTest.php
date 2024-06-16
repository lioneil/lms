<?php

namespace Assessment\Feature\Admin;

use Assessment\Models\Submission;
use Assessment\Services\SubmissionServiceInterface;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Assessment\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SubmissionsTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

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
     * @group  submissions.index
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $submissions = factory(Submission::class, 5)->create();

        // Actions
        $response = $this->get(route('submissions.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('assessment::submission.index')
                 ->assertSeeText(trans('Add Submissions'))
                 ->assertSeeText(trans('All Submissions'))
                 ->assertSeeTextInOrder($submissions->pluck('results')->toArray())
                 ->assertSeeTextInOrder($submissions->pluck('remarks')->toArray())
                 ->assertSeeTextInOrder($submissions->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.show
     * @return void
     */
    public function a_super_user_can_visit_a_submission_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $submission = factory(Submission::class, 4)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('submissions.show', $submission->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('assessment::submission.show')
                 ->assertSeeText($submission->results)
                 ->assertSeeText($submission->remarks);
        $this->assertEquals($submission->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.store
     * @return void
     */
    public function a_super_user_can_store_a_submission_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);

        // Actions
        $attributes = factory(Submission::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('submissions.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('submissions.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.edit
     * @return void
     */
    public function a_super_user_can_visit_the_edit_submission_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $submission = factory(Submission::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('submissions.edit', $submission->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewHas('resource')
                 ->assertViewIs('assessment::submission.edit')
                 ->assertSeeText(trans('Edit Submission'))
                 ->assertSeeText($submission->results)
                 ->assertSeeText($submission->remarks)
                 ->assertSeeText(trans('Update Submission'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.update
     * @return void
     */
    public function a_super_user_can_update_a_submission()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $submission = factory(Submission::class, 3)->create()->random();

        // Actions
        $attributes = factory(Submission::class)->make()->toArray();
        $response = $this->put(route('submissions.update', $submission->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('submissions.show', $submission->getKey()));
        $this->assertDatabaseHas($submission->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.trashed
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_trashed_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $submissions = factory(Submission::class, 5)->create();
        $submissions->each->delete();

        // Actions
        $response = $this->get(route('submissions.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('assessment::submission.trashed')
                 ->assertSeeText(trans('Back to Submissions'))
                 ->assertSeeText(trans('Archived Submissions'))
                 ->assertSeeTextInOrder($submissions->pluck('results')->toArray())
                 ->assertSeeTextInOrder($submissions->pluck('remarks')->toArray())
                 ->assertSeeTextInOrder($submissions->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Restore'), trans('Remove Permanently')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_submission()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $submissions = factory(Submission::class, 3)->create()->random();

        // Actions
        $response = $this->delete(
            route('submissions.destroy', $submissions->getKey()), [], ['HTTP_REFERER' => route('submissions.index')]
        );

        // Assertions
        $response->assertRedirect(route('submissions.index'));
        $this->assertSoftDeleted($submissions->getTable(), $submissions->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_multiple_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $submissions = factory(Submission::class, 3)->create();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->delete(route('submissions.destroy', $single = 'false'), $attributes);
        $submissions = $this->service->withTrashed()->whereIn('id', $submissions->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('submissions.index'));
        $submissions->each(function ($submission) {
            $this->assertSoftDeleted($submission->getTable(), $submission->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_a_submission()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $submission = factory(Submission::class, 3)->create()->random();
        $submission->delete();

        // Actions
        $response = $this->delete(route('submissions.delete', $submission->getKey()), [], ['HTTP_REFERER' => route('submissions.trashed')]);

        // Assertions
        $response->assertRedirect(route('submissions.trashed'));
        $this->assertDatabaseMissing($submission->getTable(), $submission->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_multiple_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $submissions = factory(Submission::class, 3)->create();
        $submissions->each->delete();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->delete(route('submissions.delete'), $attributes, ['HTTP_REFERER' => route('submissions.trashed')]);

        // Assertions
        $response->assertRedirect(route('submissions.trashed'));
        $submissions->each(function ($submission) {
            $this->assertDatabaseMissing($submission->getTable(), $submission->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.restore
     * @return void
     */
    public function a_super_user_can_restore_a_submission()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $submission = factory(Submission::class, 3)->create()->random();
        $submission->delete();

        // Actions
        $response = $this->patch(
            route('submissions.restore', $submission->getKey()), [], ['HTTP_REFERER' => route('submissions.trashed')]
        );
        $submission = $this->service->find($submission->getKey());

        // Assertions
        $response->assertRedirect(route('submissions.trashed'));
        $this->assertFalse($submission->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.restore
     * @return void
     */
    public function a_super_user_can_restore_multiple_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $submissions = factory(Submission::class, 3)->create();
        $submissions->each->delete();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->patch(
            route('submissions.restore'), $attributes, ['HTTP_REFERER' => route('submissions.trashed')]
        );
        $submissions = $this->service->whereIn('id', $submissions->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('submissions.trashed'));
        $submissions->each(function ($submission) {
            $this->assertFalse($submission->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.index
     * @group  user::submissions.index
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.index', 'submissions.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Submission::class, 2)->create();
        $submissions = factory(Submission::class, 2)->create(['user_id' => $user->getKey()]);

        // Actions
        $response = $this->get(route('submissions.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('assessment::submission.index')
                 ->assertSeeText(trans('All Submissions'))
                 ->assertSeeTextInOrder($submissions->pluck('results')->toArray())
                 ->assertSeeTextInOrder($submissions->pluck('remarks')->toArray())
                 ->assertSeeTextInOrder($submissions->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->remarks);
        $this->assertSame($submissions->random()->author, $user->displayname);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.trashed
     * @group  user::submissions.trashed
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_trashed_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.trashed', 'submissions.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Submission::class, 2)->create();
        $restricted->each->delete();

        $submissions = factory(Submission::class, 2)->create(['user_id' => $user->getKey()]);
        $submissions->each->delete();

        // Actions
        $response = $this->get(route('submissions.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('assessment::submission.trashed')
                 ->assertSeeText(trans('Back to Submissions'))
                 ->assertSeeText(trans('Archived Submissions'))
                 ->assertSeeTextInOrder($submissions->pluck('results')->toArray())
                 ->assertSeeTextInOrder($submissions->pluck('remarks')->toArray())
                 ->assertSeeTextInOrder($submissions->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->remarks)
                 ->assertDontSeeText($restricted->random()->author);
        $this->assertSame($submissions->random()->author, $user->displayname);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.destroy
     * @group  user:submissions.destroy
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_submission()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.destroy', 'submissions.owned']));
        $this->withPermissionsPolicy();

        $submission = factory(Submission::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('submissions.destroy', $submission->getKey()));

        // Assertions
        $response->assertRedirect(route('submissions.index'));
        $this->assertSoftDeleted($submission->getTable(), $submission->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.destroy
     * @group  user:submissions.destroy
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.index', 'submissions.destroy', 'submissions.owned']));
        $this->withPermissionsPolicy();
        $submissions = factory(Submission::class, 2)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->delete(
            route('submissions.destroy', '@null'), $attributes, ['HTTP_REFERER' => route('submissions.index')]
        );
        $submissions = $this->service->withTrashed()->whereIn('id', $submissions->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('submissions.index'));
        $submissions->each(function ($submission) {
            $this->assertSoftDeleted($submission->getTable(),$submission->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.destroy
     * @group  user:submissions.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_submission()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.destroy', 'submissions.owned']));
        $this->withPermissionsPolicy();
        $submission = factory(Submission::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('submissions.destroy', $submission->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($submission->getTable(), $submission->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.destroy
     * @group  user:submissions.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_multiple_others_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.trashed', 'submissions.owned', 'submissions.destroy']));
        $this->withPermissionsPolicy();

        $submissions = factory(Submission::class, 3)->create();
        $submissions->each->delete();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->delete(
            route('submissions.destroy', '@null'), $attributes, ['HTTP_REFERER' => route('submissions.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $submissions->each(function ($submission) {
            $this->assertDatabaseHas($submission->getTable(), $submission->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.show
     * @group  user:submissions.show
     * @return void
     */
    public function a_user_can_visit_any_submission_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.edit', 'submissions.show', 'submissions.owned', 'submissions.destroy']));
        $this->withPermissionsPolicy();

        $submission = factory(Submission::class, 5)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('submissions.show', $submission->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('assessment::submission.show')
                 ->assertSeeText($submission->results)
                 ->assertSeeText($submission->remarks);
        $this->assertEquals($submission->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.show
     * @group  user:submissions.show
     * @return void
     */
    public function a_user_cannot_edit_others_submissions_on_the_show_submission_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.edit', 'submissions.show', 'submissions.owned', 'submissions.destroy']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['submissions.edit', 'submissions.show', 'submissions.owned', 'submissions.destroy']);

        $submission = factory(Submission::class, 5)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('submissions.show', $submission->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('assessment::submission.show');
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.store
     * @group  user:submissions.store
     * @return void
     */
    public function a_user_can_store_a_submission_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.create', 'submissions.store']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Submission::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('submissions.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('submissions.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.edit
     * @group  user:submissions.edit
     * @return void
     */
    public function a_user_can_only_visit_their_owned_edit_submission_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.edit', 'submissions.update']));
        $this->withPermissionsPolicy();

        $submission = factory(Submission::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('submissions.edit', $submission->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.edit
     * @group  user:submissions.edit
     * @return void
     */
    public function a_user_cannot_visit_others_edit_submission_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.edit', 'submissions.update', 'submissions.owned']));
        $submission = factory(Submission::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('submissions.edit', $submission->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.update
     * @group  user:submissions.update
     * @return void
     */
    public function a_user_can_only_update_their_owned_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.owned', 'submissions.update']));
        $this->withPermissionsPolicy();

        $submission = factory(Submission::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = factory(Submission::class)->make()->toArray();
        $response = $this->put(route('submissions.update', $submission->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('submissions.show', $submission->getKey()));
        $this->assertDatabaseHas($submission->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.update
     * @group  user:submissions.update
     * @return void
     */
    public function a_user_cannot_update_others_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.owned', 'submissions.update']));
        $this->withPermissionsPolicy();

        $submission = factory(Submission::class, 3)->create()->random();

        // Actions
        $attributes = ['user_id' => $user->getKey()];
        $response = $this->put(route('submissions.update', $submission->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($submission->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.restore
     * @group  user:submissions.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_submission()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.owned', 'submissions.restore']));
        $this->withPermissionsPolicy();

        $submission = factory(Submission::class, 3)->create(['user_id' => $user->getKey()])->random();
        $submission->delete();

        // Actions
        $response = $this->patch(
            route('submissions.restore', $submission->getKey()), [], ['HTTP_REFERER' => route('submissions.trashed')]
        );
        $submission = $this->service->find($submission->getKey());

        // Assertions
        $response->assertRedirect(route('submissions.trashed'));
        $this->assertFalse($submission->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.restore
     * @group  user:submissions.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_multiple_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.owned', 'submissions.restore']));
        $this->withPermissionsPolicy();

        $submissions = factory(Submission::class, 3)->create(['user_id' => $user->getKey()]);
        $submissions->each->delete();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->patch(route('submissions.restore'), $attributes, ['HTTP_REFERER' => route('submissions.trashed')]);
        $submissions = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertRedirect(route('submissions.trashed'));
        $submissions->each(function ($submission) {
            $this->assertFalse($submission->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.restore
     * @group  user:submissions.restore
     * @return void
     */
    public function a_user_cannot_restore_others_submission()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.owned', 'submission.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['submissions.owned', 'submissions.restore']);
        $submission = factory(Submission::class, 3)->create(['user_id' => $otherUser->getKey()])->random();
        $submission->delete();

        // Actions
        $response = $this->patch(route('submissions.restore', $submission->getKey()), [], ['HTTP_REFERER' => route('submissions.trashed')]);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($submission->getTable(), $submission->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.restore
     * @group  user:submissions.restore
     * @return void
     */
    public function a_user_cannot_restore_others_multiple_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.owned', 'submissions.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['submissions.owned', 'submissions.restore']);
        $submissions = factory(Submission::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ]);
        $submissions->each->delete();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->patch(
            route('submissions.restore'), $attributes, ['HTTP_REFERER' => route('submissions.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $submissions->each(function ($submission) {
            $this->assertDatabaseHas($submission->getTable(), $submission->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.delete
     * @group  user:submissions.delete
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_submission()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.trashed', 'submissions.delete', 'submissions.owned']));
        $this->withPermissionsPolicy();

        $submission = factory(Submission::class, 3)->create(['user_id' => $user->getKey()])->random();
        $submission->delete();

        // Actions
        $response = $this->delete(route('submissions.delete', $submission->getKey()), [], ['HTTP_REFERER' => route('submissions.trashed')]);

        // Assertions
        $response->assertRedirect(route('submissions.trashed'));
        $this->assertDatabaseMissing($submission->getTable(), $submission->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.delete
     * @group  user:submissions.delete
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.trashed', 'submissions.delete', 'submissions.owned']));
        $this->withPermissionsPolicy();
        $submissions = factory(Submission::class, 3)->create(['user_id' => $user->getKey()]);
        $submissions->each->delete();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->delete(route('submissions.delete'), $attributes, ['HTTP_REFERER' => route('submissions.trashed')]);

        // Assertions
        $response->assertRedirect(route('submissions.trashed'));
        $submissions->each(function ($submission) {
            $this->assertDatabaseMissing($submission->getTable(), $submission->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.delete
     * @group  user:submissions.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_submission()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.trashed', 'submissions.delete', 'submissions.owned']));
        $this->withPermissionsPolicy();
        $submission = factory(Submission::class, 3)->create()->random();
        $submission->delete();

        // Actions
        $response = $this->delete(
            route('submissions.delete', $submission->getKey()), [], ['HTTP_REFERER' => route('submissions.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($submission->getTable(), $submission->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:submission
     * @group  submissions.delete
     * @group  user:submissions.delete
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_others_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.trashed', 'submissions.delete', 'submissions.owned']));
        $submissions = factory(Submission::class, 3)->create();
        $submissions->each->delete();

        // Actions
        $attributes = ['id' => $submissions->pluck('id')->toArray()];
        $response = $this->delete(
            route('submissions.delete'), $attributes, ['HTTP_REFERER' => route('submissions.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $submissions->each(function ($submission) {
            $this->assertDatabaseHas($submission->getTable(), $submission->toArray());
        });
    }
}
