<?php

namespace Announcement\Feature\Admin;

use Announcement\Models\Announcement;
use Announcement\Services\AnnouncementServiceInterface;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Taxonomy\Models\Category;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Announcement\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AnnouncementsTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker ,ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(AnnouncementServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.index
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $this->withoutExceptionHandling();
        $announcements = factory(Announcement::class, 5)->create();

        // Actions
        $response = $this->get(route('announcements.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('announcement::admin.index')
                 ->assertSeeText(trans('Add Announcement'))
                 ->assertSeeText(trans('All Announcements'))
                 ->assertSeeTextInOrder($announcements->pluck('title')->toArray())
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
    }

    /**
     * Browse test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.trashed
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_trashed_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $announcements = factory(Announcement::class, 5)->create();
        $announcements->each->delete();

        // Actions
        $response = $this->get(route('announcements.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('announcement::admin.trashed')
                 ->assertSeeText(trans('Back to Announcements'))
                 ->assertSeeText(trans('Archived Announcements'))
                 ->assertSeeTextInOrder($announcements->pluck('title')->toArray())
                 ->assertSeeTextInOrder([trans('Restore'), trans('Remove Permanently')]);
    }

    /**
     * Read Test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.show
     * @return void
     */
    public function a_super_user_can_visit_a_announcement_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $announcement = factory(Announcement::class, 4)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('announcements.show', $announcement->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('announcement::admin.show')
                 ->assertSeeText($announcement->title);
        $this->assertEquals($announcement->getKey(), $actual->getKey());
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.edit
     * @return void
     */
    public function a_super_user_can_visit_the_edit_announcement_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $announcement = factory(Announcement::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('announcements.edit', $announcement->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewHas('resource')
                 ->assertViewIs('announcement::admin.edit')
                 ->assertSeeText(trans('Edit Announcement'))
                 ->assertSeeText($announcement->title)
                 ->assertSeeText(trans('Update Announcement'));
    }

    /**
     * Test Pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.update
     * @return void
     */
    public function a_super_user_can_update_a_announcement()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $announcement = factory(Announcement::class, 3)->create()->random();

        // Actions
        $attributes = ['title' => $this->faker->words($count = 5, $asText = true)];
        $response = $this->put(route('announcements.update', $announcement->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('announcements.show', $announcement->getKey()));
        $this->assertDatabaseHas($announcement->getTable(), $attributes);
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.restore
     * @return void
     */
    public function a_super_user_can_restore_a_announcement()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $this->withoutExceptionHandling();
        $announcement = factory(Announcement::class, 3)->create()->random();
        $announcement->delete();

        // Actions
        $response = $this->patch(
            route('announcements.restore', $announcement->getKey()), [], ['HTTP_REFERER' => route('announcements.trashed')]
        );
        $announcement = $this->service->find($announcement->getKey());

        // Assertions
        $response->assertRedirect(route('announcements.trashed'));
        $this->assertFalse($announcement->trashed());
    }

    /**
     * Edit // test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.restore
     * @return void
     */
    public function a_super_user_can_restore_multiple_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $announcements = factory(Announcement::class, 3)->create();
        $announcements->each->delete();

        // Actions
        $attributes = ['id' => $announcements->pluck('id')->toArray()];
        $response = $this->patch(
            route('announcements.restore'), $attributes, ['HTTP_REFERER' => route('announcements.trashed')]
        );
        $announcements = $this->service->whereIn('id', $announcements->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('announcements.trashed'));
        $announcements->each(function ($announcement) {
            $this->assertFalse($announcement->trashed());
        });
    }

    /**
     * Add. test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.create
     * @return void
     */
    public function a_super_user_can_visit_the_create_announcement_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());

        // Actions
        $response = $this->get(route('announcements.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('announcement::admin.create')
                 ->assertSeeText(trans('Create Announcement'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('Save Announcement'));
    }

    /**
     * Add.
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.store
     * @return void
     */
    public function a_super_user_can_store_a_announcement_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());

        // Actions
        $attributes = factory(Announcement::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('announcements.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('announcements.index'));
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_a_announcement()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $announcement = factory(Announcement::class, 3)->create()->random();

        // Actions
        $response = $this->delete(
            route('announcements.destroy', $announcement->getKey()), [], ['HTTP_REFERER' => route('announcements.index')]
        );
        $announcement = $this->service->withTrashed()->find($announcement->getKey());

        // Assertions
        $response->assertRedirect(route('announcements.index'));
        $this->assertSoftDeleted($announcement->getTable(), $announcement->toArray());
    }

    /**
     * Delete. Test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_multiple_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $announcements = factory(Announcement::class, 3)->create();

        // Actions
        $attributes = ['id' => $announcements->pluck('id')->toArray()];
        $response = $this->delete(route('announcements.destroy', $single = 'false'), $attributes);
        $announcements = $this->service->withTrashed()->whereIn('id', $announcements->pluck('id')->toArray())->get();
        $response->assertRedirect(route('announcements.index'));
        $announcements->each(function ($announcements) {
            $this->assertSoftDeleted($announcements->getTable(), $announcements->toArray());
        });
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_a_announcement()
    {
         // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $this->withoutExceptionHandling();
        $announcement = factory(Announcement::class, 3)->create()->random();
        $announcement->delete();

        // Actions
        $response = $this->delete(
            route('announcements.delete', $announcement->getKey()), [], ['HTTP_REFERER' => route('announcements.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('announcements.trashed'));
        $this->assertDatabaseMissing($announcement->getTable(), $announcement->toArray());
    }

     /**
     * Test Pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_multiple_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $announcements = factory(Announcement::class, 3)->create();
        $announcements->each->delete();

        // Actions
        $attributes = ['id' => $announcements->pluck('id')->toArray()];
        $response = $this->delete(
            route('announcements.delete'), $attributes, ['HTTP_REFERER' => route('announcements.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('announcements.trashed'));
        $announcements->each(function ($announcement) {
            $this->assertDatabaseMissing($announcement->getTable(), $announcement->toArray());
        });
    }

    /**
     * Browse test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.index
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.index', 'announcements.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Announcement::class, 2)->create();
        $announcements = factory(Announcement::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);

        // Actions
        $response = $this->get(route('announcements.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('announcement::admin.index')
                 ->assertSeeText(trans('All Announcements'))
                 ->assertSeeTextInOrder($announcements->pluck('title')->toArray())
                 ->assertDontSeeText($restricted->random()->title);
        $this->assertSame(e($announcements->random()->author), e($user->displayname));
    }

    /**
     * Test Pass
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.trashed
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_trashed_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.trashed', 'announcements.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Announcement::class, 2)->create();
        $announcements = factory(Announcement::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);
        $announcements->each->delete();

        // Actions
        $response = $this->get(route('announcements.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('announcement::admin.trashed')
                 ->assertSeeText(trans('All Announcements'))
                 ->assertSeeText(trans('Archived Announcements'))
                 ->assertSeeTextInOrder($announcements->pluck('title')->toArray())
                 ->assertDontSeeText($restricted->random()->title);
        $this->assertSame($announcements->random()->author, $user->displayname);
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.show
     * @return void
     */
    public function a_user_can_visit_owned_announcement_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'announcements.edit', 'announcements.show', 'announcements.owned', 'announcements.destory'
        ]));
        $this->withPermissionsPolicy();

        $announcement = factory(Announcement::class, 3)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('announcements.show', $announcement->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('announcement::admin.show')
                 ->assertSeeText($announcement->title);
        $this->assertEquals($announcement->getKey(), $actual->getKey());
    }

    /**
     * Test pass
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.show
     * @group  user:announcements.show
     * @return void
     */
    public function a_user_can_visit_any_announcement_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'announcements.edit', 'announcements.show', 'announcements.owned', 'announcements.destroy'
        ]));
        $this->actingAs($otherUser = $this->asNonSuperAdmin([
            'announcements.edit', 'announcements.show', 'announcements.owned', 'announcements.destroy'
        ]));

        $this->withPermissionsPolicy();
        $announcement = factory(Announcement::class, 4)->create([
            'user_id' => $user->getKey(),
        ])->random();

        $announcement = factory(Announcement::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('announcements.show', $announcement->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('announcement::admin.show')
                 ->assertSeeText($announcement->title);
        $this->assertEquals($announcement->getKey(), $actual->getKey());
    }

    /**
     * test pass
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  courses.show
     * @group  user:announcements.show
     * @return void
     */
    public function a_user_cannot_edit_others_announcements_on_the_show_announcement_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'announcements.edit', 'announcements.show', 'announcements.owned', 'announcements.destroy'
        ]));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'announcements.edit', 'announcements.show', 'announcements.owned', 'announcements.destroy'
        ]);

        $announcement = factory(Announcement::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('announcements.show', $announcement->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('announcement::admin.show')
                 ->assertDontSeeText(trans('Edit'))
                 ->assertDontSeeText(trans('Move to Trash'));
    }

    /**
     * test pass
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  announcements.edit
     * @group  user:announcements.edit
     * @return void
     */
    public function a_user_can_only_visit_their_owned_edit_announcement_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.edit', 'announcements.update']));
        $this->withPermissionsPolicy();

        $announcement = factory(Announcement::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->get(route('announcements.edit', $announcement->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.edit
     * @return void
     */
    public function a_user_cannot_visit_others_edit_announcement_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.edit', 'announcements.update', 'announcements.owned']));
        $announcement = factory(Announcement::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('announcements.edit', $announcement->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.update
     * @return void
     */
    public function a_user_can_only_update_their_owned_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.owned', 'announcements.update']));
        $this->withPermissionsPolicy();
        $announcement = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['title' => $this->faker->words(5, $asText = true)];
        $response = $this->put(route('announcements.update', $announcement->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('announcements.show', $announcement->getKey()));
        $this->assertDatabaseHas($announcement->getTable(), $attributes);
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.update
     * @return void
     */
    public function a_user_cannot_update_others_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.owned', 'announcements.update']));
        $this->withPermissionsPolicy();
        $announcement = factory(Announcement::class, 3)->create()->random();

        // Actions
        $attributes = ['title' => $this->faker->words(5, $asText = true)];
        $response = $this->put(route('announcements.update', $announcement->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($announcement->getTable(), $attributes);
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_announcement()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.owned', 'announcements.restore']));
        $this->withPermissionsPolicy();
        $announcement = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()])->random();
        $announcement->delete();

        // Actions
        $response = $this->patch(
            route('announcements.restore', $announcement->getKey()), [], ['HTTP_REFERER' => route('announcements.trashed')]
        );
        $announcement = $this->service->find($announcement->getKey());

        // Assertions
        $response->assertRedirect(route('announcements.trashed'));
        $this->assertFalse($announcement->trashed());
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_multiple_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.owned', 'announcements.restore']));
        $this->withPermissionsPolicy();
        $announcements = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()]);
        $announcements->each->delete();

        // Actions
        $attributes = ['id' => $announcements->pluck('id')->toArray()];
        $response = $this->patch(
            route('announcements.restore'), $attributes, ['HTTP_REFERER' => route('announcements.trashed')]
        );
        $announcements = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertRedirect(route('announcements.trashed'));
        $announcements->each(function ($announcement) {
            $this->assertFalse($announcement->trashed());
        });
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.restore
     * @return void
     */
    public function a_user_cannot_restore_others_announcement()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.owned', 'announcements.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['announcements.owned', 'announcements.restore']);
        $announcement = factory(Announcement::class, 3)->create(['user_id' => $otherUser->getKey()])->random();
        $announcement->delete();

        // Actions
        $response = $this->patch(
            route('announcements.restore', $announcement->getKey()), [], ['HTTP_REFERER' => route('announcements.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($announcement->getTable(), $announcement->toArray());
    }

    /**
     * Add test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.create
     * @return void
     */
    public function a_user_can_visit_the_create_announcement_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.create']));
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->get(route('announcements.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('announcement::admin.create')
                 ->assertSeeText(trans('Create Announcement'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('Save Announcement'));
    }

    /**
     * Add test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.store
     * @return void
     */
    public function a_user_can_store_a_announcement_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.create', 'announcements.store']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Announcement::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('announcements.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('announcements.index'));
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.destroy
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_announcement()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.destroy', 'announcements.owned']));
        $this->withPermissionsPolicy();
        $announcement = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('announcements.destroy', $announcement->getKey()));

        // Assertions
        $response->assertRedirect(route('announcements.index'));
        $this->assertSoftDeleted($announcement->getTable(), $announcement->toArray());
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.destroy
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.destroy', 'announcements.owned']));
        $this->withPermissionsPolicy();
        $announcements = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $announcements->pluck('id')->toArray()];
        $response = $this->delete(route('announcements.destroy', $single = 'false'), $attributes);

        // Assertions
        $response->assertRedirect(route('announcements.index'));
        $announcements->each(function ($announcement) {
            $this->assertSoftDeleted($announcement->getTable(), $announcement->toArray());
        });
    }

    /**
     * Delete test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.destroy', 'announcements.owned']));
        $this->withPermissionsPolicy();
        $announcement = factory(announcement::class, 3)->create()->random();
        $announcement->delete();

        // Actions
        $response = $this->delete(route('announcements.destroy', $announcement->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($announcement->getTable(), $announcement->toArray());
    }

    /**
     * Delete test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_multiple_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.destroy', 'announcements.owned']));
        $this->withPermissionsPolicy();
        $announcements = factory(Announcement::class, 3)->create();
        $announcements->each->delete();

        // Actions
        $attributes = ['id' => $announcements->pluck('id')->toArray()];
        $response = $this->delete(
            route('announcements.destroy', $single = 'false'), $attributes);

        // Assertions
        $response->assertForbidden();
        $announcements->each(function ($announcement) {
            $this->assertDatabaseHas($announcement->getTable(), $announcement->toArray());
        });
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.delete
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_announcement()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.trashed', 'announcements.delete', 'announcements.owned']));
        $this->withPermissionsPolicy();
        $announcement = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()])->random();
        $announcement->delete();

        // Actions
        $response = $this->delete(
            route('announcements.delete', $announcement->getKey()), [], ['HTTP_REFERER' => route('announcements.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('announcements.trashed'));
        $this->assertDatabaseMissing($announcement->getTable(), $announcement->toArray());
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.delete
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.trashed', 'announcements.delete', 'announcements.owned']));
        $this->withPermissionsPolicy();
        $announcements = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()]);
        $announcements->each->delete();

        // Actions
        $attributes = ['id' => $announcements->pluck('id')->toArray()];
        $response = $this->delete(
            route('announcements.delete'), $attributes, ['HTTP_REFERER' => route('announcements.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('announcements.trashed'));
        $announcements->each(function ($announcement) {
            $this->assertDatabaseMissing($announcement->getTable(), $announcement->toArray());
        });
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_announcement()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.trashed', 'announcements.delete', 'announcements.owned']));
        $this->withPermissionsPolicy();
        $announcement = factory(Announcement::class, 3)->create()->random();
        $announcement->delete();

        // Actions
        $response = $this->delete(
            route('announcements.delete', $announcement->getKey()), [], ['HTTP_REFERER' => route('announcements.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($announcement->getTable(), $announcement->toArray());
    }

    /**
     * Delete test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:announcements.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_multiple_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['announcements.trashed', 'announcements.delete', 'announcements.owned']));
        $this->withPermissionsPolicy();
        $announcements = factory(Announcement::class, 3)->create();
        $announcements->each->delete();

        // Actions
        $attributes = ['id' => $announcements->pluck('id')->toArray()];
        $response = $this->delete(
            route('announcements.delete'), $attributes, ['HTTP_REFERER' => route('announcements.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $announcements->each(function ($announcement) {
            $this->assertDatabaseHas($announcement->getTable(), $announcement->toArray());
        });
    }
}
