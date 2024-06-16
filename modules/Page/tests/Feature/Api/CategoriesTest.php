<?php

namespace Page\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Page\Services\CategoryServiceInterface;
use Taxonomy\Models\Category;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Page\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CategoriesTest extends TestCase
{
    use ActingAsUser, RefreshDatabase, WithFaker, WithPermissionsPolicy;

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
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_page_categories()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.index', 'categories.owned']), ['categories.index']);
        $this->withPermissionsPolicy();

        $categories = factory(Category::class, 2)->create([
            'user_id' => $user->getKey(),
            'type' => 'page',
        ])->random();

        // Actions
        $response = $this->get(route('api.pages.categories.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [[
                    'user_id' => $user->getKey(),
                ]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'name', 'alias', 'code',
                        'description', 'icon', 'type',
                        'user_id', 'created', 'modified',
                        'deleted',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_trashed_page_categories()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.trashed']), ['categories.trashed']);
        $this->withPermissionsPolicy();

        $categories = factory(Category::class, 2)->create([
            'user_id' => $user->getKey(),
            'type' => 'page',
        ]);
        $categories->each->delete();

        // Actions
        $response = $this->get(route('api.pages.categories.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [[
                    'user_id' => $user->getKey(),
                 ]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'name',
                        'user_id',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_can_visit_their_owned_page_category_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.show']), ['categories.show']);
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 2)->create([
            'user_id' => $user->getKey(),
            'type' => 'page',
        ])->random();

        // Actions
        $response = $this->get(route('api.pages.categories.show', $category->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'user_id' => $user->getKey(),
                 ]])
                 ->assertJsonStructure([
                    'data' => [
                        'name',
                        'user_id',
                    ],
                ]);
    }

     /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_can_visit_any_page_category_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.show']), ['categories.show']);
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 2)->create([
            'user_id' => $user->getKey(),
            'type' => 'page',
        ])->random();

        // Actions
        $response = $this->get(route('api.pages.categories.show', $category->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'user_id' => $user->getKey(),
                 ]])
                 ->assertJsonStructure([
                    'data' => [
                        'name',
                        'user_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_can_store_a_page_category_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.store']), ['categories.store']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Category::class)->make([
            'user_id' => $user->getKey(),
            'type' => 'page',
        ])->toArray();
        $response = $this->post(route('api.pages.categories.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_can_only_update_their_owned_page_categories()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.owned', 'categories.update']), ['categories.update']);
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 3)->create([
            'user_id' => $user->getKey(),
            'type' => 'page',
        ])->random();

        // Actions
        $attributes = factory(Category::class)->make()->toArray();
        $response = $this->put(route('api.pages.categories.update', $category->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($category->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_cannot_update_page_categories_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.owned', 'categories.update']), ['categories.update']);
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 3)->create()->random();

        // Actions
        $attributes = factory(category::class)->make()->toArray();
        $response = $this->put(route('api.pages.categories.update', $category->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($category->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_can_only_restore_owned_page_category()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.restore']), ['categories.restore']);
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 3)->create([
            'user_id' => $user->getKey(),

        ])->random();

        // Actions
        $response = $this->patch(route('api.pages.categories.restore', $category->getKey()));
        $category = $this->service->find($category->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($category->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_can_only_multiple_restore_owned_page_categories()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.restore']), ['categories.restore']);
        $this->withPermissionsPolicy();

        $categories = factory(Category::class, 3)->create([
            'user_id' => $user->getKey(),
            'type' => 'page',
        ])->random();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->patch(route('api.pages.categories.restore'), $attributes);
        $categories = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $categories->each(function ($category) {
            $this->assertFalse($category->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_cannot_restore_page_category_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.restore']), ['categories.restore']);
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['categories.owned', 'categories.restore']);
        $category = factory(Category::class, 3)->create([
            'user_id' => $otherUser->getKey(),
            'type' => 'page',
        ])->random();

        // Actions
        $response = $this->patch(route('api.pages.categories.restore', $category->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($category->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_cannot_multiple_restore_page_categories_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.restore']), ['categories.restore']);
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['categories.owned', 'categories.restore']);
        $categories = factory(Category::class, 3)->create([
            'user_id' => $otherUser->getKey(),
            'type' => 'page',
        ]);

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->patch(route('api.pages.categories.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
        $categories->each(function ($category) {
            $this->assertDatabaseHas($category->getTable(), $category->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_page_category()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.destroy']), ['categories.destroy']);
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 3)->create([
            'user_id' => $user->getKey(),
            'type' => 'page',
        ])->random();

        // Actions
        $response = $this->delete(route('api.pages.categories.destroy', $category->getKey()));
        $category = $this->service->withTrashed()->find($category->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($category->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_page_categories()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.destroy']), ['categories.destroy']);
        $this->withPermissionsPolicy();

        $categories = factory(Category::class, 3)->create([
            'user_id' => $user->getKey(),
            'type' => 'page',
        ])->random();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(route('api.pages.categories.destroy', 'null'), $attributes);
        $categories = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $categories->each(function ($category) {
            $this->assertSoftDeleted($category->getTable(), $category->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_cannot_soft_delete_page_category_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.destroy']), ['categories.destroy']);
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.pages.categories.destroy', $category->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($category->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_cannot_multiple_soft_delete_page_categories_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.destroy']), ['categories.destroy']);
        $this->withPermissionsPolicy();

        $categories = factory(Category::class, 3)->create();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(route('api.pages.categories.destroy', 'null'), $attributes);

        // Assertions
        $response->assertForbidden();
        $categories->each(function ($category) {
            $this->assertDatabaseHas($category->getTable(), $category->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_page_category()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.delete']), ['categories.delete']);
        $this->withPermissionsPolicy();

        $category = factory(Category::class, 2)->create([
            'user_id' => $user->getKey(),
            'type' => 'page',
        ])->random();

        // Actions
        $response = $this->delete(route('api.pages.categories.delete', $category->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($category->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_page_categories()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.delete']), ['categories.delete']);
        $this->withPermissionsPolicy();

        $categories = factory(Category::class, 3)->create([
            'user_id' => $user->getKey(),
            'type' => 'page',
        ])->random();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(route('api.pages.categories.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $categories->each(function ($category) {
            $this->assertDatabaseMissing($category->getTable(), $category->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_cannot_permanently_delete_category_owned_by_other_page_categories()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['categories.delete']), ['categories.delete']);
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.pages.categories.delete', $category->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($category->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page:category
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_page_categories_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['categories.delete']), ['categories.delete']);
        $this->withPermissionsPolicy();

        $categories = factory(Category::class, 3)->create();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(route('api.pages.categories.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
        $categories->each(function ($category) {
            $this->assertDatabaseHas($category->getTable(), $category->toArray());
        });
    }
}
