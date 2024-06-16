<?php

namespace Tests\Material\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Material\Models\Material;
use Material\Services\MaterialServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Material\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class MaterialsTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker ,ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(MaterialServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.index
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $materials = factory(Material::class, 5)->create();

        // Actions
        $response = $this->get(route('materials.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('material::admin.index')
                 ->assertSeeText(trans('Add Material'))
                 ->assertSeeText(trans('All Materials'))
                 ->assertSeeTextInOrder($materials->pluck('title')->toArray())
                 ->assertSeeTextInOrder($materials->pluck('uri')->toArray())
                 ->assertSeeTextInOrder($materials->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
    }


    /**
     * Browse test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.trashed
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_trashed_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $materials = factory(Material::class, 5)->create();
        $materials->each->delete();

        // Actions
        $response = $this->get(route('materials.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('material::admin.trashed')
                 ->assertSeeText(trans('Back to Materials'))
                 ->assertSeeText(trans('Archived Materials'))
                 ->assertSeeTextInOrder($materials->pluck('title')->toArray())
                 ->assertSeeTextInOrder($materials->pluck('uri')->toArray())
                 ->assertSeeTextInOrder($materials->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Restore'), trans('Remove Permanently')]);

    }

    /**
     * Read Test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.show
     * @return void
     */
    public function a_super_user_can_visit_a_material_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $material = factory(Material::class, 4)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('materials.show', $material->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('material::admin.show')
                 ->assertSeeText($material->title)
                 ->assertSeeText($material->uri)
                 ->assertSeeText($material->pathname);
        $this->assertEquals($material->getKey(), $actual->getKey());
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.edit
     * @return void
     */
    public function a_super_user_can_visit_the_edit_material_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $material = factory(Material::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('materials.edit', $material->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewHas('resource')
                 ->assertViewIs('material::admin.edit')
                 ->assertSeeText(trans('Edit Material'))
                 ->assertSeeText($material->title)
                 ->assertSeeText($material->uri)
                 ->assertSeeText($material->pathname)
                 ->assertSeeText(trans('Update Material'));

    }

    /**
     * Test Pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.update
     * @return void
     */
    public function a_super_user_can_update_a_material()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        $this->actingAs($user = $this->asSuperAdmin());
        $material = factory(Material::class, 3)->create()->random();

        // Actions
        $attributes = ['title' => $this->faker->words($count = 5, $asText = true)];
        $response = $this->put(route('materials.update', $material->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('materials.show', $material->getKey()));
        $this->assertDatabaseHas($material->getTable(), $attributes);
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.restore
     * @return void
     */
    public function a_super_user_can_restore_a_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $material = factory(Material::class, 3)->create()->random();
        $material->delete();

        // Actions
        $response = $this->patch(
            route('materials.restore', $material->getKey()), [], ['HTTP_REFERER' => route('materials.trashed')]
        );
        $material = $this->service->find($material->getKey());

        // Assertions
        $response->assertRedirect(route('materials.trashed'));
        $this->assertFalse($material->trashed());
    }

    /**
     * Edit // test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.restore
     * @return void
     */
    public function a_super_user_can_restore_multiple_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $materials = factory(Material::class, 3)->create();
        $materials->each->delete();

        // Actions
        $attributes = ['id' => $materials->pluck('id')->toArray()];
        $response = $this->patch(
            route('materials.restore'), $attributes, ['HTTP_REFERER' => route('materials.trashed')]
        );
        $materials = $this->service->whereIn('id', $materials->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('materials.trashed'));
        $materials->each(function ($material) {
            $this->assertFalse($material->trashed());
        });
    }

    /**
     * Add. test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.create
     * @return void
     */
    public function a_super_user_can_visit_the_create_material_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());

        // Actions
        $response = $this->get(route('materials.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('material::admin.create')
                 ->assertSeeText(trans('Create Material'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('URI'))
                 ->assertSeeText(trans('Pathname'))
                 ->assertSeeText(trans('Save Material'));

    }

    /**
     * Add. Failed asserting that a row in the table [coursewares] matches the attributes {
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.store
     * @return void
     */
    public function a_super_user_can_store_a_material_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());

        // Actions
        $attributes = factory(Material::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('materials.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('materials.index'));
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_a_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $material = factory(Material::class, 3)->create()->random();

        // Actions
        $response = $this->delete(
            route('materials.destroy', $material->getKey()), [], ['HTTP_REFERER' => route('materials.index')]
        );
        $material = $this->service->withTrashed()->find($material->getKey());

        // Assertions
        $response->assertRedirect(route('materials.index'));
        $this->assertSoftDeleted($material->getTable(), $material->toArray());
    }

    /**
     * Delete. Test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_multiple_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $materials = factory(Material::class, 3)->create();

        // Actions
        $attributes = ['id' => $materials->pluck('id')->toArray()];
        $response = $this->delete(route('materials.destroy', $single = 'false'), $attributes);
        $materials = $this->service->withTrashed()->whereIn('id', $materials->pluck('id')->toArray())->get();
        $response->assertRedirect(route('materials.index'));
        $materials->each(function ($materials) {
            $this->assertSoftDeleted($materials->getTable(), $materials->toArray());
        });
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_a_material()
    {
         // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $material = factory(Material::class, 3)->create()->random();
        $material->delete();

        // Actions
        $response = $this->delete(
            route('materials.delete', $material->getKey()), [], ['HTTP_REFERER' => route('materials.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('materials.trashed'));
        $this->assertDatabaseMissing($material->getTable(), $material->toArray());
    }

    /**
     * Test Pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_multiple_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $materials = factory(Material::class, 3)->create();
        $materials->each->delete();

        // Actions
        $attributes = ['id' => $materials->pluck('id')->toArray()];
        $response = $this->delete(
            route('materials.delete'), $attributes, ['HTTP_REFERER' => route('materials.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('materials.trashed'));
        $materials->each(function ($material) {
            $this->assertDatabaseMissing($material->getTable(), $material->toArray());
        });
    }

    /**
     * Browse test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.index
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.index', 'materials.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Material::class, 2)->create();
        $materials = factory(Material::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);

        // Actions
        $response = $this->get(route('materials.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('material::admin.index')
                 ->assertSeeText(trans('All Materials'))
                 ->assertSeeTextInOrder($materials->pluck('title')->toArray())
                 ->assertSeeTextInOrder($materials->pluck('uri')->toArray())
                 ->assertSeeTextInOrder($materials->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->title)
                 ->assertDontSeeText($restricted->random()->uri)
                 ->assertDontSeeText(e($restricted->random()->author));
        $this->assertSame(e($materials->random()->author), e($user->displayname));

    }

    /**
     * Test Pass
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.trashed
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_trashed_materials()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.trashed', 'materials.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Material::class, 2)->create();
        $materials = factory(Material::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);
        $materials->each->delete();

        // Actions
        $response = $this->get(route('materials.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('material::admin.trashed')
                 ->assertSeeText(trans('All Materials'))
                 ->assertSeeText(trans('Archived Materials'))
                 ->assertSeeTextInOrder($materials->pluck('title')->toArray())
                 ->assertSeeTextInOrder($materials->pluck('uri')->toArray())
                 ->assertSeeTextInOrder($materials->pluck('author')->toArray())
                 ->assertDontSeeText($restricted->random()->title)
                 ->assertDontSeeText($restricted->random()->uri)
                 ->assertDontSeeText($restricted->random()->author);
        $this->assertSame($materials->random()->author, $user->displayname);
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.show
     * @return void
     */
    public function a_user_can_visit_owned_material_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'materials.edit', 'materials.show', 'materials.owned', 'materials.destory'
        ]));
        $this->withPermissionsPolicy();

        $material = factory(Material::class, 3)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('materials.show', $material->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('material::admin.show')
                 ->assertSeeText($material->title)
                 ->assertSeeText($material->uri)
                 ->assertSeeText($material->pathname);
        $this->assertEquals($material->getKey(), $actual->getKey());
    }

    /**
     * Test pass
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.show
     * @group  user:materials.show
     * @return void
     */
    public function a_user_can_visit_any_material_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'materials.edit', 'materials.show', 'materials.owned', 'materials.destroy'
        ]));
        $this->actingAs($otherUser = $this->asNonSuperAdmin([
            'materials.edit', 'materials.show', 'materials.owned', 'materials.destroy'
        ]));

        $this->withPermissionsPolicy();
        $material = factory(Material::class, 4)->create([
            'user_id' => $user->getKey(),
        ])->random();

        $material = factory(Material::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('materials.show', $material->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('material::admin.show')
                 ->assertSeeText($material->title)
                 ->assertSeeText($material->uri)
                 ->assertSeeText($material->pathname);
        $this->assertEquals($material->getKey(), $actual->getKey());
    }

    /**
     * test pass
     * @test
     * @group  feature
     * @group  feature:material
     * @group  courses.show
     * @group  user:materials.show
     * @return void
     */
    public function a_user_cannot_edit_others_materials_on_the_show_material_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'materials.edit', 'materials.show', 'materials.owned', 'materials.destroy'
        ]));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'materials.edit', 'materials.show', 'materials.owned', 'materials.destroy'
        ]);

        $material = factory(Material::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('materials.show', $material->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('material::admin.show')
                 ->assertDontSeeText(trans('Edit'))
                 ->assertDontSeeText(trans('Move to Trash'));
    }

    /**
     * test pass
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.edit
     * @group  user:materials.edit
     * @return void
     */
    public function a_user_can_only_visit_their_owned_edit_material_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.edit', 'materials.update']));
        $this->withPermissionsPolicy();

        $material = factory(Material::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->get(route('materials.edit', $material->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  usermaterials.edit
     * @return void
     */
    public function a_user_cannot_visit_others_edit_material_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.edit', 'materials.update', 'materials.owned']));
        $material = factory(Material::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('materials.edit', $material->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.update
     * @return void
     */
    public function a_user_can_only_update_their_owned_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.owned', 'materials.update']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['title' => $this->faker->words(5, $asText = true)];
        $response = $this->put(route('materials.update', $material->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('materials.show', $material->getKey()));
        $this->assertDatabaseHas($material->getTable(), $attributes);
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.update
     * @return void
     */
    public function a_user_cannot_update_others_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.owned', 'materials.update']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 3)->create()->random();

        // Actions
        $attributes = ['title' => $this->faker->words(5, $asText = true)];
        $response = $this->put(route('materials.update', $material->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($material->getTable(), $attributes);
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.owned', 'materials.restore']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 3)->create(['user_id' => $user->getKey()])->random();
        $material->delete();

        // Actions
        $response = $this->patch(
            route('materials.restore', $material->getKey()), [], ['HTTP_REFERER' => route('materials.trashed')]
        );
        $material = $this->service->find($material->getKey());

        // Assertions
        $response->assertRedirect(route('materials.trashed'));
        $this->assertFalse($material->trashed());
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_multiple_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.owned', 'materials.restore']));
        $this->withPermissionsPolicy();
        $materials = factory(Material::class, 3)->create(['user_id' => $user->getKey()]);
        $materials->each->delete();

        // Actions
        $attributes = ['id' => $materials->pluck('id')->toArray()];
        $response = $this->patch(
            route('materials.restore'), $attributes, ['HTTP_REFERER' => route('materials.trashed')]
        );
        $materials = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertRedirect(route('materials.trashed'));
        $materials->each(function ($material) {
            $this->assertFalse($material->trashed());
        });
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.restore
     * @return void
     */
    public function a_user_cannot_restore_others_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.owned', 'materials.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['materials.owned', 'materials.restore']);
        $material = factory(Material::class, 3)->create(['user_id' => $otherUser->getKey()])->random();
        $material->delete();

        // Actions
        $response = $this->patch(
            route('materials.restore', $material->getKey()), [], ['HTTP_REFERER' => route('materials.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($material->getTable(), $material->toArray());
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.restore
     * @return void
     */
    public function a_user_cannot_restore_others_multiple_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.owned', 'materials.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['materials.owned', 'materials.restore']);
        $materials = factory(Material::class, 3)->create(['user_id' => $otherUser->getKey()]);
        $materials->each->delete();

        // Actions
        $attributes = ['id' => $materials->pluck('id')->toArray()];
        $response = $this->patch(
            route('materials.restore'), $attributes, ['HTTP_REFERER' => route('materials.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $materials->each(function ($material) {
            $this->assertDatabaseHas($material->getTable(), $material->toArray());
        });
    }

    /**
     * Add test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.create
     * @return void
     */
    public function a_user_can_visit_the_create_material_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.create']));
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->get(route('materials.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('material::admin.create')
                 ->assertSeeText(trans('Create Material'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('URI'))
                 ->assertSeeText(trans('Pathname'))
                 ->assertSeeText(trans('Save Material'));

    }

    /**
     * Add test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.store
     * @return void
     */
    public function a_user_can_store_a_material_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.create', 'materials.store']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Material::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('materials.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('materials.index'));
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.destroy
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.destroy', 'materials.owned']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('materials.destroy', $material->getKey()));
        $material = $this->service->withTrashed()->find($material->getKey());

        // Assertions
        $response->assertRedirect(route('materials.index'));
        $this->assertSoftDeleted($material->getTable(), $material->toArray());
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.destroy
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.destroy', 'materials.owned']));
        $this->withPermissionsPolicy();
        $materials = factory(Material::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $materials->pluck('id')->toArray()];
        $response = $this->delete(route('materials.destroy', $single = 'false'), $attributes);

        // Assertions
        $response->assertRedirect(route('materials.index'));
        $materials->each(function ($material) {
            $this->assertSoftDeleted($material->getTable(), $material->toArray());
        });
    }

    /**
     * Delete test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.destroy', 'materials.owned']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 3)->create()->random();
        $material->delete();

        // Actions
        $response = $this->delete(route('materials.destroy', $material->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($material->getTable(), $material->toArray());
    }

    /**
     * Delete test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_multiple_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.destroy', 'materials.owned']));
        $this->withPermissionsPolicy();
        $materials = factory(Material::class, 3)->create();
        $materials->each->delete();

        // Actions
        $attributes = ['id' => $materials->pluck('id')->toArray()];
        $response = $this->delete(
            route('materials.destroy', $single = 'false'), $attributes);

        // Assertions
        $response->assertForbidden();
        $materials->each(function ($material) {
            $this->assertDatabaseHas($material->getTable(), $material->toArray());
        });
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.delete
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.trashed', 'materials.delete', 'materials.owned']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 3)->create(['user_id' => $user->getKey()])->random();
        $material->delete();

        // Actions
        $response = $this->delete(
            route('materials.delete', $material->getKey()), [], ['HTTP_REFERER' => route('materials.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('materials.trashed'));
        $this->assertDatabaseMissing($material->getTable(), $material->toArray());
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.delete
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.trashed', 'materials.delete', 'materials.owned']));
        $this->withPermissionsPolicy();
        $materials = factory(Material::class, 3)->create(['user_id' => $user->getKey()]);
        $materials->each->delete();

        // Actions
        $attributes = ['id' => $materials->pluck('id')->toArray()];
        $response = $this->delete(
            route('materials.delete'), $attributes, ['HTTP_REFERER' => route('materials.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('materials.trashed'));
        $materials->each(function ($material) {
            $this->assertDatabaseMissing($material->getTable(), $material->toArray());
        });
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_material()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.trashed', 'materials.delete', 'materials.owned']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 3)->create()->random();
        $material->delete();

        // Actions
        $response = $this->delete(
            route('materials.delete', $material->getKey()), [], ['HTTP_REFERER' => route('materials.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($material->getTable(), $material->toArray());
    }

    /**
     * Delete test pass
     *
     * @test
     * @group  feature
     * @group  feature:material
     * @group  user:materials.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_multiple_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['materials.trashed', 'materials.delete', 'materials.owned']));
        $this->withPermissionsPolicy();
        $materials = factory(Material::class, 3)->create();
        $materials->each->delete();

        // Actions
        $attributes = ['id' => $materials->pluck('id')->toArray()];
        $response = $this->delete(
            route('materials.delete'), $attributes, ['HTTP_REFERER' => route('materials.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $materials->each(function ($material) {
            $this->assertDatabaseHas($material->getTable(), $material->toArray());
        });
    }

}
