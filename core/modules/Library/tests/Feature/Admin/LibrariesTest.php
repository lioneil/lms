<?php

namespace Library\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Library\Models\Library;
use Library\Services\LibraryServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Library\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class LibrariesTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(LibraryServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

     /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.index
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $libraries = factory(Library::class, 5)->create();

        // Actions
        $response = $this->get(route('libraries.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('library::admin.index')
                 ->assertSeeText(trans('Add Library'))
                 ->assertSeeText(trans('All Libraries'))
                 ->assertSeeTextInOrder($libraries->pluck('name')->toArray())
                 ->assertSeeTextInOrder($libraries->pluck('uri')->toArray())
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.trashed
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_trashed_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $libraries = factory(Library::class, 5)->create();
        $libraries->each->delete();

        // Actions
        $response = $this->get(route('libraries.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('library::admin.trashed')
                 ->assertSeeText(trans('Back to Libraries'))
                 ->assertSeeText(trans('Archived Libraries'))
                 ->assertSeeTextInOrder($libraries->pluck('name')->toArray())
                 ->assertSeeTextInOrder($libraries->pluck('uri')->toArray())
                 ->assertSeeTextInOrder([trans('Restore'), trans('Remove Permanently')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.show
     * @return void
     */
    public function a_super_user_can_visit_a_library_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $library = factory(Library::class, 4)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('libraries.show', $library->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('library::admin.show')
                 ->assertSeeText($library->name)
                 ->assertSeeText($library->uri)
                 ->assertSeeText($library->pathname);
        $this->assertEquals($library->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.edit
     * @return void
     */
    public function a_super_user_can_visit_the_edit_library_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $library = factory(Library::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('libraries.edit', $library->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewHas('resource')
                 ->assertViewIs('library::admin.edit')
                 ->assertSeeText(trans('Edit Library'))
                 ->assertSeeText($library->name)
                 ->assertSeeText($library->uri)
                 ->assertSeeText($library->pathname)
                 ->assertSeeText(trans('Update Library'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.update
     * @return void
     */
    public function a_super_user_can_update_a_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $library = factory(Library::class, 3)->create()->random();

        // Actions
        $attributes = ['name' => $this->faker->words($count = 5, $asText = true)];
        $response = $this->put(route('libraries.update', $library->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('libraries.show', $library->getKey()));
        $this->assertDatabaseHas($library->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.restore
     * @return void
     */
    public function a_super_user_can_restore_a_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $library = factory(Library::class, 3)->create()->random();
        $library->delete();

        // Actions
        $response = $this->patch(
            route('libraries.restore', $library->getKey()), [], ['HTTP_REFERER' => route('libraries.trashed')]
        );
        $library = $this->service->find($library->getKey());

        // Assertions
        $response->assertRedirect(route('libraries.trashed'));
        $this->assertFalse($library->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.restore
     * @return void
     */
    public function a_super_user_can_restore_multiple_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $libraries = factory(Library::class, 3)->create();
        $libraries->each->delete();

        // Actions
        $attributes = ['id' => $libraries->pluck('id')->toArray()];
        $response = $this->patch(
            route('libraries.restore'), $attributes, ['HTTP_REFERER' => route('libraries.trashed')]
        );
        $libraries = $this->service->whereIn('id', $libraries->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('libraries.trashed'));
        $libraries->each(function ($library) {
            $this->assertFalse($library->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.create
     * @return void
     */
    public function a_super_user_can_visit_the_create_library_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());

        // Actions
        $response = $this->get(route('libraries.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('library::admin.create')
                 ->assertSeeText(trans('Create Library'))
                 ->assertSeeText(trans('Name'))
                 ->assertSeeText(trans('URI'))
                 ->assertSeeText(trans('Pathname'))
                 ->assertSeeText(trans('Save Library'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.store
     * @return void
     */
    public function a_super_user_can_store_a_library_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);

        // Actions
        $attributes = factory(Library::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('libraries.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('libraries.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_a_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $library = factory(Library::class, 3)->create()->random();

        // Actions
        $response = $this->delete(
            route('libraries.destroy', $library->getKey()), [], ['HTTP_REFERER' => route('libraries.index')]
        );
        $library = $this->service->withTrashed()->find($library->getKey());

        // Assertions
        $response->assertRedirect(route('libraries.index'));
        $this->assertSoftDeleted($library->getTable(), $library->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_multiple_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $libraries = factory(Library::class, 3)->create();

        // Actions
        $attributes = ['id' => $libraries->pluck('id')->toArray()];
        $response = $this->delete(route('libraries.destroy', $single = 'false'), $attributes);
        $libraries = $this->service->withTrashed()->whereIn('id', $libraries->pluck('id')->toArray())->get();
        $response->assertRedirect(route('libraries.index'));
        $libraries->each(function ($libraries) {
            $this->assertSoftDeleted($libraries->getTable(), $libraries->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_a_library()
    {
         // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $library = factory(Library::class, 3)->create()->random();
        $library->delete();

        // Actions
        $response = $this->delete(
            route('libraries.delete', $library->getKey()), [], ['HTTP_REFERER' => route('libraries.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('libraries.trashed'));
        $this->assertDatabaseMissing($library->getTable(), $library->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_multiple_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $libraries = factory(Library::class, 3)->create();
        $libraries->each->delete();

        // Actions
        $attributes = ['id' => $libraries->pluck('id')->toArray()];
        $response = $this->delete(
            route('libraries.delete'), $attributes, ['HTTP_REFERER' => route('libraries.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('libraries.trashed'));
        $libraries->each(function ($library) {
            $this->assertDatabaseMissing($library->getTable(), $library->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.index
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.index', 'libraries.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Library::class, 2)->create();
        $libraries = factory(Library::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);

        // Actions
        $response = $this->get(route('libraries.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('library::admin.index')
                 ->assertSeeText(trans('All Libraries'))
                 ->assertSeeTextInOrder($libraries->pluck('name')->toArray())
                 ->assertSeeTextInOrder($libraries->pluck('url')->toArray())
                 ->assertSeeTextInOrder($libraries->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->name)
                 ->assertDontSeeText($restricted->random()->url)
                 ->assertDontSeeText(e($restricted->random()->author));
        $this->assertSame(e($libraries->random()->author), e($user->displayname));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.trashed
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_trashed_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.trashed', 'libraries.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Library::class, 2)->create();
        $libraries = factory(Library::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);
        $libraries->each->delete();

        // Actions
        $response = $this->get(route('libraries.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('library::admin.trashed')
                 ->assertSeeText(trans('All Libraries'))
                 ->assertSeeText(trans('Archived Libraries'))
                 ->assertSeeTextInOrder($libraries->pluck('name')->toArray())
                 ->assertSeeTextInOrder($libraries->pluck('url')->toArray())
                 ->assertSeeTextInOrder($libraries->pluck('author')->toArray())
                 ->assertDontSeeText($restricted->random()->name)
                 ->assertDontSeeText($restricted->random()->url)
                 ->assertDontSeeText($restricted->random()->author);
        $this->assertSame($libraries->random()->author, $user->displayname);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.show
     * @return void
     */
    public function a_user_can_visit_owned_material_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'libraries.edit', 'libraries.show', 'libraries.owned', 'libraries.destory'
        ]));
        $this->withPermissionsPolicy();

        $library = factory(Library::class, 3)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('libraries.show', $library->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('library::admin.show')
                 ->assertSeeText($library->title)
                 ->assertSeeText($library->uri)
                 ->assertSeeText($library->pathname);
        $this->assertEquals($library->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.show
     * @group  user:libraries.show
     * @return void
     */
    public function a_user_can_visit_any_library_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'libraries.edit', 'libraries.show', 'libraries.owned', 'libraries.destroy'
        ]));
        $this->actingAs($otherUser = $this->asNonSuperAdmin([
            'libraries.edit', 'libraries.show', 'libraries.owned', 'libraries.destroy'
        ]));

        $this->withPermissionsPolicy();
        $library = factory(Library::class, 4)->create([
            'user_id' => $user->getKey(),
        ])->random();

        $library = factory(Library::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('libraries.show', $library->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('library::admin.show')
                 ->assertSeeText($library->title)
                 ->assertSeeText($library->uri)
                 ->assertSeeText($library->pathname);
        $this->assertEquals($library->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.show
     * @group  user:libraries.show
     * @return void
     */
    public function a_user_cannot_edit_others_libraries_on_the_show_library_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'libraries.edit', 'libraries.show', 'libraries.owned', 'libraries.destroy'
        ]));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'libraries.edit', 'libraries.show', 'libraries.owned', 'libraries.destroy'
        ]);

        $library = factory(Library::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('libraries.show', $library->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('library::admin.show')
                 ->assertDontSeeText(trans('Edit'))
                 ->assertDontSeeText(trans('Move to Trash'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  libraries.edit
     * @group  user:libraries.edit
     * @return void
     */
    public function a_user_can_only_visit_their_owned_edit_library_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.edit', 'libraries.update']));
        $this->withPermissionsPolicy();

        $library = factory(Library::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->get(route('libraries.edit', $library->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user.libraries.edit
     * @return void
     */
    public function a_user_cannot_visit_others_edit_library_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.edit', 'libraries.update', 'libraries.owned']));
        $library = factory(Library::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('libraries.edit', $library->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.update
     * @return void
     */
    public function a_user_can_only_update_their_owned_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.owned', 'libraries.update']));
        $this->withPermissionsPolicy();
        $library = factory(Library::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['name' => $this->faker->words(5, $asText = true)];
        $response = $this->put(route('libraries.update', $library->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('libraries.show', $library->getKey()));
        $this->assertDatabaseHas($library->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.update
     * @return void
     */
    public function a_user_cannot_update_others_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.owned', 'libraries.update']));
        $this->withPermissionsPolicy();
        $library = factory(Library::class, 3)->create()->random();

        // Actions
        $attributes = ['name' => $this->faker->words(5, $asText = true)];
        $response = $this->put(route('libraries.update', $library->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($library->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_library()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.owned', 'libraries.restore']));
        $this->withPermissionsPolicy();
        $library = factory(Library::class, 3)->create(['user_id' => $user->getKey()])->random();
        $library->delete();

        // Actions
        $response = $this->patch(
            route('libraries.restore', $library->getKey()), [], ['HTTP_REFERER' => route('libraries.trashed')]
        );
        $library = $this->service->find($library->getKey());

        // Assertions
        $response->assertRedirect(route('libraries.trashed'));
        $this->assertFalse($library->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_multiple_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.owned', 'libraries.restore']));
        $this->withPermissionsPolicy();
        $libraries = factory(Library::class, 3)->create(['user_id' => $user->getKey()]);
        $libraries->each->delete();

        // Actions
        $attributes = ['id' => $libraries->pluck('id')->toArray()];
        $response = $this->patch(
            route('libraries.restore'), $attributes, ['HTTP_REFERER' => route('libraries.trashed')]
        );
        $libraries = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertRedirect(route('libraries.trashed'));
        $libraries->each(function ($library) {
            $this->assertFalse($library->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.restore
     * @return void
     */
    public function a_user_cannot_restore_others_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.owned', 'libraries.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['libraries.owned', 'libraries.restore']);
        $library = factory(Library::class, 3)->create(['user_id' => $otherUser->getKey()])->random();
        $library->delete();

        // Actions
        $response = $this->patch(
            route('libraries.restore', $library->getKey()), [], ['HTTP_REFERER' => route('libraries.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($library->getTable(), $library->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.restore
     * @return void
     */
    public function a_user_cannot_restore_others_multiple_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.owned', 'libraries.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['libraries.owned', 'libraries.restore']);
        $libraries = factory(Library::class, 3)->create(['user_id' => $otherUser->getKey()]);
        $libraries->each->delete();

        // Actions
        $attributes = ['id' => $libraries->pluck('id')->toArray()];
        $response = $this->patch(
            route('libraries.restore'), $attributes, ['HTTP_REFERER' => route('libraries.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $libraries->each(function ($library) {
            $this->assertDatabaseHas($library->getTable(), $library->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.create
     * @return void
     */
    public function a_user_can_visit_the_create_library_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.create']));
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->get(route('libraries.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('library::admin.create')
                 ->assertSeeText(trans('Create Library'))
                 ->assertSeeText(trans('Name'))
                 ->assertSeeText(trans('URI'))
                 ->assertSeeText(trans('Pathname'))
                 ->assertSeeText(trans('Save Library'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.store
     * @return void
     */
    public function a_user_can_store_a_library_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.create', 'libraries.store']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Library::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('libraries.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('libraries.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.destroy
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_library()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.destroy', 'libraries.owned']));
        $this->withPermissionsPolicy();
        $library = factory(Library::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('libraries.destroy', $library->getKey()));

        // Assertions
        $response->assertRedirect(route('libraries.index'));
        $this->assertSoftDeleted($library->getTable(), $library->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_library()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.destroy', 'libraries.owned']));
        $this->withPermissionsPolicy();
        $library = factory(Library::class, 3)->create()->random();
        $library->delete();

        // Actions
        $response = $this->delete(route('libraries.destroy', $library->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($library->getTable(), $library->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_multiple_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.destroy', 'libraries.owned']));
        $this->withPermissionsPolicy();
        $libraries = factory(Library::class, 3)->create();
        $libraries->each->delete();

        // Actions
        $attributes = ['id' => $libraries->pluck('id')->toArray()];
        $response = $this->delete(
            route('libraries.destroy', $single = 'false'), $attributes);

        // Assertions
        $response->assertForbidden();
        $libraries->each(function ($library) {
            $this->assertDatabaseHas($library->getTable(), $library->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.delete
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_library()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.trashed', 'libraries.delete', 'libraries.owned']));
        $this->withPermissionsPolicy();
        $library = factory(Library::class, 3)->create(['user_id' => $user->getKey()])->random();
        $library->delete();

        // Actions
        $response = $this->delete(
            route('libraries.delete', $library->getKey()), [], ['HTTP_REFERER' => route('libraries.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('libraries.trashed'));
        $this->assertDatabaseMissing($library->getTable(), $library->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.delete
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.trashed', 'libraries.delete', 'libraries.owned']));
        $this->withPermissionsPolicy();
        $libraries = factory(Library::class, 3)->create(['user_id' => $user->getKey()]);
        $libraries->each->delete();

        // Actions
        $attributes = ['id' => $libraries->pluck('id')->toArray()];
        $response = $this->delete(
            route('libraries.delete'), $attributes, ['HTTP_REFERER' => route('libraries.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('libraries.trashed'));
        $libraries->each(function ($library) {
            $this->assertDatabaseMissing($library->getTable(), $library->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.trashed', 'libraries.delete', 'libraries.owned']));
        $this->withPermissionsPolicy();
        $library = factory(Library::class, 3)->create()->random();
        $library->delete();

        // Actions
        $response = $this->delete(
            route('libraries.delete', $library->getKey()), [], ['HTTP_REFERER' => route('libraries.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($library->getTable(), $library->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:library
     * @group  user:libraries.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_multiple_libraries()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['libraries.trashed', 'libraries.delete', 'libraries.owned']));
        $this->withPermissionsPolicy();
        $libraries = factory(Library::class, 3)->create();
        $libraries->each->delete();

        // Actions
        $attributes = ['id' => $libraries->pluck('id')->toArray()];
        $response = $this->delete(
            route('libraries.delete'), $attributes, ['HTTP_REFERER' => route('libraries.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $libraries->each(function ($library) {
            $this->assertDatabaseHas($library->getTable(), $library->toArray());
        });
    }
}
