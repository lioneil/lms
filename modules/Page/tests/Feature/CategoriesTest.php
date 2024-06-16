<?php

namespace Page\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Page\Services\CategoryServiceInterface;
use Taxonomy\Models\Category;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Page\Feature
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CategoriesTest extends TestCase
{
    use ActingAsUser, DatabaseMigrations, RefreshDatabase, WithFaker, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CategoryServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.index
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $categories = factory(Category::class, 5)->create();

        // Actions
        $response = $this->get(route('categories.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('page::categories.index')
                 ->assertSeeText(trans('Add Categories'))
                 ->assertSeeText(trans('All Categories'))
                 ->assertSeeTextInOrder($categories->pluck('name')->toArray())
                 ->assertSeeTextInOrder($categories->pluck('code')->toArray())
                 ->assertSeeTextInOrder($categories->pluck('description')->toArray())
                 ->assertSeeTextInOrder($categories->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.trashed
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_trashed_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $categories = factory(Category::class, 5)->create();
        $categories->each->delete();

        // Actions
        $response = $this->get(route('categories.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('page::categories.trashed')
                 ->assertSeeText(trans('Back to Categories'))
                 ->assertSeeText(trans('Archived Categories'))
                 ->assertSeeTextInOrder($categories->pluck('name')->toArray())
                 ->assertSeeTextInOrder($categories->pluck('code')->toArray())
                 ->assertSeeTextInOrder($categories->pluck('description')->toArray())
                 ->assertSeeTextInOrder($categories->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Restore'), trans('Remove Permanently')]);

    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.show
     * @return void
     */
    public function a_super_user_can_visit_a_category_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $category = factory(Category::class, 4)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('categories.show', $category->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('page::categories.show')
                 ->assertSeeText($category->name)
                 ->assertSeeText($category->code)
                 ->assertSeeText($category->description);
        $this->assertEquals($category->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.create
     * @return void
     */
    public function a_super_user_can_visit_the_create_category_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);

        // Actions
        $response = $this->get(route('categories.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('page::categories.create')
                 ->assertSeeText(trans('Create Category'))
                 ->assertSeeText(trans('Name'))
                 ->assertSeeText(trans('Code'))
                 ->assertSeeText(trans('Description'))
                 ->assertSeeText(trans('Save Category'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.edit
     * @return void
     */
    public function a_super_user_can_visit_the_edit_category_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $category = factory(Category::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('categories.edit', $category->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewHas('resource')
                 ->assertViewIs('page::categories.edit')
                 ->assertSeeText(trans('Edit Category'))
                 ->assertSeeText($category->name)
                 ->assertSeeText($category->code)
                 ->assertSeeText($category->description)
                 ->assertSeeText(trans('Update Category'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.restore
     * @return void
     */
    public function a_super_user_can_restore_a_category()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $category = factory(Category::class, 3)->create()->random();
        $category->delete();

        // Actions
        $response = $this->patch(route('categories.restore', $category->getKey()), [], ['HTTP_REFERER' => route('categories.trashed')]);
        $category = $this->service->find($category->getKey());

        // Assertions
        $response->assertRedirect(route('categories.trashed'));
        $this->assertFalse($category->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.restore
     * @return void
     */
    public function a_super_user_can_restore_multiple_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $categories = factory(Category::class, 3)->create();
        $categories->each->delete();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->patch(route('categories.restore'), $attributes, ['HTTP_REFERER' => route('categories.trashed')]);
        $categories = $this->service->whereIn('id', $categories->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('categories.trashed'));
        $categories->each(function ($category) {
            $this->assertFalse($category->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.update
     * @return void
     */
    public function a_super_user_can_update_a_category()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $category = factory(Category::class, 3)->create()->random();

        // Actions
        $attributes = factory(Category::class)->make()->toArray();
        $response = $this->put(route('categories.update', $category->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('categories.show', $category->getKey()));
        $this->assertDatabaseHas($category->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_a_category()
    {
        // Arrangement
        $this->actingAs($user = $this->superAdmin);
        $category = factory(Category::class, 3)->create()->random();

        // Actions
        $response = $this->delete(
            route('categories.destroy', $category->getKey()), [], ['HTTP_REFERER' => route('categories.index')]
        );

        // Assertions
        $response->assertRedirect(route('categories.index'));
        $this->assertSoftDeleted($category->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_multiple_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $categories = factory(Category::class, 3)->create();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(route('categories.destroy', $single = 'false'), $attributes);
        $categories = $this->service->withTrashed()->whereIn('id', $categories->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('categories.index'));
        $categories->each(function ($category) {
            $this->assertSoftDeleted($category->getTable(), $category->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_an_category()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $category = factory(Category::class, 3)->create()->random();
        $category->delete();

        // Actions
        $response = $this->delete(
            route('categories.delete', $category->getKey()), [],
            ['HTTP_REFERER' => route('categories.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('categories.trashed'));
        $this->assertDatabaseMissing($category->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_multiple_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $categories = factory(Category::class, 3)->create();
        $categories->each->delete();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(
            route('categories.delete'), $attributes, ['HTTP_REFERER' => route('categories.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('categories.trashed'));
        $categories->each(function ($category) {
            $this->assertDatabaseMissing($category->getTable(), $category->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.store
     * @return void
     */
    public function a_super_user_can_store_an_category_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);

        // Actions
        $attributes = factory(Category::class)->make([
            'user_id' => $user->getKey(),
        ])->toArray();
        $response = $this->post(route('categories.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('categories.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.index
     * @group  user::categories.index
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.index', 'categories.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Category::class, 2)->create();
        $categories = factory(Category::class, 2)->create(['user_id' => $user->getKey()]);

        // Actions
        $response = $this->get(route('categories.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('page::categories.index')
                 ->assertSeeText(trans('All Categories'))
                 ->assertSeeTextInOrder($categories->pluck('name')->toArray())
                 ->assertSeeTextInOrder($categories->pluck('code')->toArray())
                 ->assertSeeTextInOrder($categories->pluck('description')->toArray())
                 ->assertSeeTextInOrder($categories->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->name)
                 ->assertDontSeeText($restricted->random()->code)
                 ->assertDontSeeText($restricted->random()->description);
        $this->assertSame($categories->random()->author, $user->displayname);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.owned
     * @group  user:categories.owned
     * @return void
     */
    public function a_user_can_visit_owned_categories_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.edit', 'categories.show', 'categories.owned', 'categories.destroy']));
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('categories.show', $category->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('page::categories.show')
                 ->assertSeeText($category->title)
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
        $this->assertEquals($category->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.show
     * @group  user:categories.show
     * @return void
     */
    public function a_user_can_visit_any_category_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.edit', 'categories.show', 'categories.owned', 'categories.destroy']));
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 4)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('categories.show', $category->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('page::categories.show')
                 ->assertSeeText($category->name)
                 ->assertSeeText($category->code)
                 ->assertSeeText($category->description);
        $this->assertEquals($category->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.create
     * @group  user:categories.create
     * @return void
     */
    public function a_user_can_visit_the_create_forum_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.create']));
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->get(route('categories.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('page::categories.create')
                 ->assertSeeText(trans('Create Category'))
                 ->assertSeeText(trans('Name'))
                 ->assertSeeText(trans('Code'))
                 ->assertSeeText(trans('Description'))
                 ->assertSeeText(trans('Save Category'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.store
     * @group  user:categories.store
     * @return void
     */
    public function a_user_can_store_an_category_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.create', 'categories.store']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Category::class)->make([
            'user_id' => $user->getKey(),
        ])->toArray();
        $response = $this->post(route('categories.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('categories.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.show
     * @group  user:categories.show
     * @return void
     */
    public function a_user_cannot_edit_others_pages_on_the_show_category_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'categories.edit', 'categories.show', 'categories.owned', 'categories.destroy'
        ]));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'categories.edit', 'categories.show', 'categories.owned', 'categories.destroy'
        ]);

        $category = factory(Category::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('categories.show', $category->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('page::categories.show')
                 ->assertDontSeeText(trans('Edit'))
                 ->assertDontSeeText(trans('Move to Trash'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.edit
     * @group  user:categories.edit
     * @return void
     */
    public function a_user_can_only_visit_their_owned_edit_category_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.edit', 'categories.update']));
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('categories.edit', $category->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.edit
     * @group  user:categories.edit
     * @return void
     */
    public function a_user_cannot_visit_others_edit_category_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.edit', 'categories.update', 'categories.owned']));
        $category = factory(Category::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('categories.edit', $category->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.update
     * @group  user:categories.update
     * @return void
     */
    public function a_user_can_only_update_their_owned_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.owned', 'categories.update']));
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = factory(Category::class)->make()->toArray();
        $response = $this->put(route('categories.update', $category->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('categories.show', $category->getKey()));
        $this->assertDatabaseHas($category->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.update
     * @group  user:categories.update
     * @return void
     */
    public function a_user_cannot_update_others_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.owned', 'categories.update']));
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 3)->create()->random();

        // Actions
        $attributes = ['title' => $this->faker->words($count = 5, $asText = true)];
        $response = $this->put(route('categories.update', $category->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($category->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.restore
     * @group  user:categories.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_category()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.owned', 'categories.restore']));
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 3)->create([
            'user_id' => $user->getKey(),
        ])->random();
        $category->delete();

        // Actions
        $response = $this->patch(
            route('categories.restore', $category->getKey()), [], ['HTTP_REFERER' => route('categories.trashed')]
        );
        $category = $this->service->find($category->getKey());

        // Assertions
        $response->assertRedirect(route('categories.trashed'));
        $this->assertFalse($category->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.restore
     * @group  user:categories.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_multiple_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.owned', 'categories.restore']));
        $this->withPermissionsPolicy();

        $categories = factory(Category::class, 3)->create([
            'user_id' => $user->getKey(),
        ]);
        $categories->each->delete();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->patch(
            route('categories.restore'), $attributes, ['HTTP_REFERER' => route('categories.trashed')]
        );
        $categories = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertRedirect(route('categories.trashed'));
        $categories->each(function ($category) {
            $this->assertFalse($category->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.restore
     * @group  user:categories.restore
     * @return void
     */
    public function a_user_cannot_restore_others_category()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.owned', 'categories.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['categories.owned', 'categories.restore']);
        $category = factory(Category::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();
        $category->delete();

        // Actions
        $response = $this->patch(
            route('categories.restore', $category->getKey()), [], ['HTTP_REFERER' => route('categories.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($category->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.restore
     * @group  user:categories.restore
     * @return void
     */
    public function a_user_cannot_restore_others_multiple_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.owned', 'categories.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['categories.owned', 'categories.restore']);
        $categories = factory(Category::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ]);
        $categories->each->delete();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->patch(
            route('categories.restore'), $attributes, ['HTTP_REFERER' => route('categories.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $categories->each(function ($category) {
            $this->assertDatabaseHas($category->getTable(), $category->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.destroy
     * @group  user:categories.destroy
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_category()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.destroy', 'categories.owned']));
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('categories.destroy', $category->getKey()));

        // Assertions
        $response->assertRedirect(route('categories.index'));
        $this->assertSoftDeleted($category->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.destroy
     * @group  user:categories.destroy
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.index', 'categories.destroy', 'categories.owned']));
        $this->withPermissionsPolicy();
        $categories = factory(Category::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(
            route('categories.destroy', '@null'), $attributes, ['HTTP_REFERER' => route('categories.index')]
        );
        $categories = $this->service->withTrashed()->whereIn('id', $categories->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('categories.index'));
        $categories->each(function ($category) {
            $this->assertSoftDeleted($category->getTable(), $category->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.destroy
     * @group  user:categories.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_category()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.destroy', 'categories.owned']));
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('categories.destroy', $category->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($category->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.destroy
     * @group  user:categories.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_multiple_others_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.trashed', 'categories.destroy', 'categories.owned']));
        $this->withPermissionsPolicy();
        $categories = factory(Category::class, 3)->create();
        $categories->each->delete();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(
            route('categories.destroy', '@null'), $attributes, ['HTTP_REFERER' => route('categories.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $categories->each(function ($category) {
            $this->assertDatabaseHas($category->getTable(), $category->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.delete
     * @group  user:categories.delete
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_category()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.trashed', 'categories.delete', 'categories.owned']));
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 3)->create([
            'user_id' => $user->getKey(),
        ])->random();
        $category->delete();

        // Actions
        $response = $this->delete(
            route('categories.delete', $category->getKey()), [], ['HTTP_REFERER' => route('categories.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('categories.trashed'));
        $this->assertDatabaseMissing($category->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.delete
     * @group  user:categories.delete
     * @return void
     */
    public function a_user_can_only_multiple_permanetly_delete_owned_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.trashed', 'categories.delete', 'categories.owned']));
        $this->withPermissionsPolicy();
        $categories = factory(Category::class, 3)->create([
            'user_id' => $user->getKey(),
        ]);
        $categories->each->delete();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(
            route('categories.delete'), $attributes, ['HTTP_REFERER' => route('categories.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('categories.trashed'));
        $categories->each(function ($category) {
            $this->assertDatabaseMissing($category->getTable(), $category->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.delete
     * @group  user:categories.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_category()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.trashed', 'categories.delete', 'categories.owned']));
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 3)->create()->random();
        $category->delete();

        // Actions
        $response = $this->delete(
            route('categories.delete', $category->getKey()), [], ['HTTP_REFERER' => route('categories.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($category->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:category
     * @group  categories.delete
     * @group  user:categories.delete
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_others_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.trashed', 'categories.delete', 'categories.owned']));
        $this->withPermissionsPolicy();
        $categories = factory(Category::class, 3)->create();
        $categories->each->delete();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(
            route('categories.delete'), $attributes, ['HTTP_REFERER' => route('categories.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $categories->each(function ($category) {
            $this->assertDatabaseHas($category->getTable(), $category->toArray());
        });
    }
}
