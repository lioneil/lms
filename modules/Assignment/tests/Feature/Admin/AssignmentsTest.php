<?php

namespace Tests\Assignment\Feature\Admin;

use Assignment\Models\Assignment;
use Assignment\Services\AssignmentServiceInterface;
use Core\Application\Permissions\PermissionsPolicy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Assignment\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AssignmentsTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp() : void
    {
        parent::setUp();

        $this->service = $this->app->make(AssignmentServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.index
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_assignments()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        $this->actingAs($user = $this->superAdmin);
        $assignments = factory(Assignment::class, 5)->create();

        // Actions
        $response = $this->get(route('assignments.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('assignment::admin.index')
                 ->assertSeeText(trans('Add Assignments'))
                 ->assertSeeText(trans('All Assignments'))
                 ->assertSeeTextInOrder($assignments->pluck('title')->toArray())
                 ->assertSeeTextInOrder($assignments->pluck('uri')->toArray())
                 ->assertSeeTextInOrder($assignments->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.trashed
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_trashed_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $assignments = factory(Assignment::class, 5)->create();
        $assignments->each->delete();

        // Actions
        $response = $this->get(route('assignments.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('assignment::admin.trashed')
                 ->assertSeeText(trans('Back to Assignments'))
                 ->assertSeeText(trans('Archived Assignments'))
                 ->assertSeeTextInOrder($assignments->pluck('title')->toArray())
                 ->assertSeeTextInOrder($assignments->pluck('uri')->toArray())
                 ->assertSeeTextInOrder($assignments->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Restore'), trans('Remove Permanently')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.show
     * @return void
     */
    public function a_super_user_can_visit_an_assignment_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $assignment = factory(Assignment::class, 4)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('assignments.show', $assignment->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('assignment::admin.show')
                 ->assertSeeText($assignment->title)
                 ->assertSeeText($assignment->uri)
                 ->assertSeeText($assignment->path);
        $this->assertEquals($assignment->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.edit
     * @return void
     */
    public function a_super_user_can_visit_the_edit_assignment_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $assignment = factory(Assignment::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('assignments.edit', $assignment->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewHas('resource')
                 ->assertViewIs('assignment::admin.edit')
                 ->assertSeeText(trans('Edit Assignment'))
                 ->assertSeeText($assignment->title)
                 ->assertSeeText($assignment->uri)
                 ->assertSeeText($assignment->pathname)
                 ->assertSeeText(trans('Update Assignment'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.update
     * @return void
     */
    public function a_super_user_can_update_an_assignment()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $assignment = factory(Assignment::class, 3)->create()->random();

        // Actions
        $attributes = factory(Assignment::class)->make()->toArray();
        $attributes['file'] = UploadedFile::fake()->create('tmp.text');
        $response = $this->put(route('assignments.update', $assignment->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('assignments.show', $assignment->getKey()));
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('file', 'uri', 'pathname')->toArray());

    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.restore
     * @return void
     */
    public function a_super_user_can_restore_an_assignment()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $assignment = factory(Assignment::class, 3)->create()->random();
        $assignment->delete();

        // Actions
        $response = $this->patch(
            route('assignments.restore', $assignment->getKey()), [], ['HTTP_REFERER' => route('assignments.trashed')]
        );
        $assignment = $this->service->find($assignment->getKey());

        // Assertions
        $response->assertRedirect(route('assignments.trashed'));
        $this->assertFalse($assignment->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.restore
     * @return void
     */
    public function a_super_user_can_restore_multiple_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $assignments = factory(Assignment::class, 3)->create();
        $assignments->each->delete();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->patch(
            route('assignments.restore'), $attributes, ['HTTP_REFERER' => route('assignments.trashed')]
        );
        $assignments = $this->service->whereIn('id', $assignments->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('assignments.trashed'));
        $assignments->each(function ($assignment) {
            $this->assertFalse($assignment->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.create
     * @return void
     */
    public function a_super_user_can_visit_the_create_assignment_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);

        // Actions
        $response = $this->get(route('assignments.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('assignment::admin.create')
                 ->assertSeeText(trans('Create Assignment'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('URI'))
                 ->assertSeeText(trans('Pathname'))
                 ->assertSeeText(trans('Save Assignment'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.store
     * @return void
     */
    public function a_super_user_can_store_an_assignment_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);

        // Actions
        $attributes = factory(Assignment::class)->make(['user_id' => $user->getKey()])->toArray();
        $attributes['file'] = UploadedFile::fake()->create('tmp.text');
        $response = $this->post(route('assignments.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('file', 'uri', 'pathname')->toArray());
        $response->assertRedirect(route('assignments.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_an_assignment()
    {
        // Arrangement
        $this->actingAs($user = $this->superAdmin);
        $assignment = factory(Assignment::class, 3)->create()->random();

        // Actions
        $response = $this->delete(
            route('assignments.destroy', $assignment->getKey()), [], ['HTTP_REFERER' => route('assignments.index')]
        );

        // Assertions
        $response->assertRedirect(route('assignments.index'));
        $this->assertSoftDeleted($assignment->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_multiple_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $assignments = factory(Assignment::class, 3)->create();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->delete(route('assignments.destroy', $single = 'false'), $attributes);
        $assignments = $this->service->withTrashed()->whereIn('id', $assignments->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('assignments.index'));
        $assignments->each(function ($assignment) {
            $this->assertSoftDeleted($assignment->getTable(), $assignment->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_an_assignment()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $assignment = factory(Assignment::class, 3)->create()->random();
        $assignment->delete();

        // Actions
        $response = $this->delete(
            route('assignments.delete', $assignment->getKey()), [], ['HTTP_REFERER' => route('assignments.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('assignments.trashed'));
        $this->assertDatabaseMissing($assignment->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_multiple_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $assignments = factory(Assignment::class, 3)->create();
        $assignments->each->delete();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->delete(
            route('assignments.delete'), $attributes, ['HTTP_REFERER' => route('assignments.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('assignments.trashed'));
        $assignments->each(function ($assignment) {
            $this->assertDatabaseMissing($assignment->getTable(), $assignment->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.index
     * @group  user:assignments.index
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.index', 'assignments.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Assignment::class, 2)->create();
        $assignments = factory(Assignment::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);

        // Actions
        $response = $this->get(route('assignments.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('assignment::admin.index')
                 ->assertSeeText(trans('All Assignments'))
                 ->assertSeeTextInOrder($assignments->pluck('title')->toArray())
                 ->assertSeeTextInOrder($assignments->pluck('uri')->toArray())
                 ->assertSeeTextInOrder($assignments->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->title)
                 ->assertDontSeeText($restricted->random()->uri)
                 ->assertDontSeeText(e($restricted->random()->author));
        $this->assertSame(e($assignments->random()->author), e($user->displayname));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.trashed
     * @group  user:assignments.trashed
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_trashed_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.trashed', 'assignments.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Assignment::class, 2)->create();
        $restricted->each->delete();
        $assignments = factory(Assignment::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);
        $assignments->each->delete();

        // Actions
        $response = $this->get(route('assignments.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('assignment::admin.trashed')
                 ->assertSeeText(trans('Back to Assignments'))
                 ->assertSeeText(trans('Archived Assignments'))
                 ->assertSeeTextInOrder($assignments->pluck('title')->toArray())
                 ->assertSeeTextInOrder($assignments->pluck('uri')->toArray())
                 ->assertSeeTextInOrder($assignments->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->title)
                 ->assertDontSeeText($restricted->random()->uri)
                 ->assertDontSeeText($restricted->random()->author);
        $this->assertSame($assignments->random()->author, $user->displayname);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.owned
     * @group  user:assignments.owned
     * @return void
     */
    public function a_user_can_visit_owned_assignment_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'assignments.edit', 'assignments.show', 'assignments.owned', 'assignments.destroy'
        ]));
        $this->withPermissionsPolicy();

        $assignment = factory(Assignment::class, 3)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('assignments.show', $assignment->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('assignment::admin.show')
                 ->assertSeeText($assignment->title)
                 ->assertSeeText($assignment->uri)
                 ->assertSeeText($assignment->path)
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
        $this->assertEquals($assignment->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.show
     * @group  user:assignments.show
     * @return void
     */
    public function a_user_can_visit_any_assignment_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'assignments.edit', 'assignments.show', 'assignments.owned', 'assignments.destroy'
        ]));
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 4)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('assignments.show', $assignment->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('assignment::admin.show')
                 ->assertSeeText($assignment->title)
                 ->assertSeeText($assignment->uri)
                 ->assertSeeText($assignment->path);
        $this->assertEquals($assignment->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.show
     * @group  user:assignments.show
     * @return void
     */
    public function a_user_cannot_edit_others_assignments_on_the_show_assignment_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'assignments.edit', 'assignments.show', 'assignments.owned', 'assignments.destroy'
        ]));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'assignments.edit', 'assignments.show', 'assignments.owned', 'assignments.destroy'
        ]);
        $assignment = factory(Assignment::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('assignments.show', $assignment->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('assignment::admin.show')
                 ->assertDontSeeText(trans('Edit'))
                 ->assertDontSeeText(trans('Move to Trash'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.edit
     * @group  user:assignments.edit
     * @return void
     */
    public function a_user_can_only_visit_their_owned_edit_assignment_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.edit', 'assignments.update']));
        $this->withPermissionsPolicy();

        $assignment = factory(Assignment::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->get(route('assignments.edit', $assignment->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.edit
     * @group  user:assignments.edit
     * @return void
     */
    public function a_user_cannot_visit_others_edit_assignment_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.edit', 'assignments.update', 'assignments.owned']));
        $assignment = factory(Assignment::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('assignments.edit', $assignment->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.update
     * @group  user:assignments.update
     * @return void
     */
    public function a_user_can_only_update_their_owned_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.owned', 'assignments.update']));
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = factory(Assignment::class)->make()->toArray();
        $attributes['file'] = UploadedFile::fake()->create('tmp.text');
        $response = $this->put(route('assignments.update', $assignment->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('assignments.show', $assignment->getKey()));
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('file', 'uri', 'pathname')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.update
     * @group  user:assignments.update
     * @return void
     */
    public function a_user_cannot_update_others_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.owned', 'assignments.update']));
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 3)->create()->random();

        // Actions
        $attributes = ['title' => $this->faker->words($count = 5, $asText = true)];
        $response = $this->put(route('assignments.update', $assignment->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($assignment->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.restore
     * @group  user:assignments.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_assignment()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.owned', 'assignments.restore']));
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();
        $assignment->delete();

        // Actions
        $response = $this->patch(
            route('assignments.restore', $assignment->getKey()), [], ['HTTP_REFERER' => route('assignments.trashed')]
        );
        $assignment = $this->service->find($assignment->getKey());

        // Assertions
        $response->assertRedirect(route('assignments.trashed'));
        $this->assertFalse($assignment->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.restore
     * @group  user:assignments.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_multiple_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.owned', 'assignments.restore']));
        $this->withPermissionsPolicy();
        $assignments = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()]);
        $assignments->each->delete();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->patch(
            route('assignments.restore'), $attributes, ['HTTP_REFERER' => route('assignments.trashed')]
        );
        $assignments = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertRedirect(route('assignments.trashed'));
        $assignments->each(function ($assignment) {
            $this->assertFalse($assignment->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.restore
     * @group  user:assignments.restore
     * @return void
     */
    public function a_user_cannot_restore_others_assignment()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.owned', 'assignments.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['assignments.owned', 'assignments.restore']);
        $assignment = factory(Assignment::class, 3)->create(['user_id' => $otherUser->getKey()])->random();
        $assignment->delete();

        // Actions
        $response = $this->patch(
            route('assignments.restore', $assignment->getKey()), [], ['HTTP_REFERER' => route('assignments.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($assignment->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.restore
     * @group  user:assignments.restore
     * @return void
     */
    public function a_user_cannot_restore_others_multiple_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.owned', 'assignments.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['assignments.owned', 'assignments.restore']);
        $assignments = factory(Assignment::class, 3)->create(['user_id' => $otherUser->getKey()]);
        $assignments->each->delete();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->patch(
            route('assignments.restore'), $attributes, ['HTTP_REFERER' => route('assignments.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $assignments->each(function ($assignment) {
            $this->assertDatabaseHas($assignment->getTable(), $assignment->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.create
     * @group  user:assignments.create
     * @return void
     */
    public function a_user_can_visit_the_create_assignment_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.create']));
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->get(route('assignments.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('assignment::admin.create')
                 ->assertSeeText(trans('Create Assignment'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('URI'))
                 ->assertSeeText(trans('Pathname'))
                 ->assertSeeText(trans('Save Assignment'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.store
     * @group  user:assignments.store
     * @return void
     */
    public function a_user_can_store_an_assignment_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.create', 'assignments.store']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Assignment::class)->make(['user_id' => $user->getKey()])->toArray();
        $attributes['file'] = UploadedFile::fake()->create('tmp.text');
        $response = $this->post(route('assignments.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('file', 'uri', 'pathname')->toArray());
        $response->assertRedirect(route('assignments.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.destroy
     * @group  user:assignments.destroy
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_assignment()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.destroy', 'assignments.owned']));
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('assignments.destroy', $assignment->getKey()));

        // Assertions
        $response->assertRedirect(route('assignments.index'));
        $this->assertSoftDeleted($assignment->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.destroy
     * @group  user:assignments.destroy
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.destroy', 'assignments.owned']));
        $this->withPermissionsPolicy();
        $assignments = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->delete(route('assignments.destroy', $single = 'false'), $attributes);

        // Assertions
        $response->assertRedirect(route('assignments.index'));
        $assignments->each(function ($assignment) {
            $this->assertSoftDeleted($assignment->getTable(), $assignment->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.destroy
     * @group  user:assignments.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_assignment()
    {
        // Arrangemnts
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.destroy', 'assignments.owned']));
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('assignments.destroy', $assignment->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($assignment->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.destroy
     * @group  user:assignments.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_multiple_others_assignments()
    {
        // Arrangemnts
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.destroy', 'assignments.owned']));
        $this->withPermissionsPolicy();
        $assignments = factory(Assignment::class, 3)->create();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->delete(route('assignments.destroy', $single = 'false'), $attributes);

        // Assertions
        $response->assertForbidden();
        $assignments->each(function ($assignment) {
            $this->assertDatabaseHas($assignment->getTable(), $assignment->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.delete
     * @group  user:assignments.delete
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_assignment()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.trashed', 'assignments.delete', 'assignments.owned']));
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();
        $assignment->delete();

        // Actions
        $response = $this->delete(
            route('assignments.delete', $assignment->getKey()), [], ['HTTP_REFERER' => route('assignments.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('assignments.trashed'));
        $this->assertDatabaseMissing($assignment->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.delete
     * @group  user:assignments.delete
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.trashed', 'assignments.delete', 'assignments.owned']));
        $this->withPermissionsPolicy();
        $assignments = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()]);
        $assignments->each->delete();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->delete(
            route('assignments.delete'), $attributes, ['HTTP_REFERER' => route('assignments.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('assignments.trashed'));
        $assignments->each(function ($assignment) {
            $this->assertDatabaseMissing($assignment->getTable(), $assignment->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.delete
     * @group  user:assignments.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_assignment()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.trashed', 'assignments.delete', 'assignments.owned']));
        $this->withPermissionsPolicy();
        $assignment = factory(Assignment::class, 3)->create()->random();
        $assignment->delete();

        // Actions
        $response = $this->delete(
            route('assignments.delete', $assignment->getKey()), [], ['HTTP_REFERER' => route('assignments.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($assignment->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:assignment
     * @group  assignments.delete
     * @group  user:assignments.delete
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_others_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['assignments.trashed', 'assignments.delete', 'assignments.owned']));
        $this->withPermissionsPolicy();
        $assignments = factory(Assignment::class, 3)->create();
        $assignments->each->delete();

        // Actions
        $attributes = ['id' => $assignments->pluck('id')->toArray()];
        $response = $this->delete(
            route('assignments.delete'), $attributes, ['HTTP_REFERER' => route('assignments.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $assignments->each(function ($assignment) {
            $this->assertDatabaseHas($assignment->getTable(), $assignment->toArray());
        });
    }
}
