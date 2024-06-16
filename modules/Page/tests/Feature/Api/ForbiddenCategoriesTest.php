<?php

namespace Page\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Taxonomy\Models\Category;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use Page\Services\CategoryServiceInterface;

/**
 * @package Page\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ForbiddenCategoriesTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CategoryServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:category
     * @group  feature:api:category:forbidden
     * @return void
     */
    public function an_upermitted_user_cannot_retrieve_the_paginated_list_of_all_categories()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->post(route('api.pages.categories.index'));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:category
     * @group  feature:api:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_store_a_category_to_database()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Category::class)->make()->toArray();
        $response = $this->post(route('api.pages.categories.store'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:category
     * @group  feature:api:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_a_single_category()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 2)->create()->random();

        // Actions
        $response = $this->get(route('api.pages.categories.show', $category->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:category
     * @group  feature:api:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_update_a_category()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 2)->create()->random();

        // Actions
        $attributes = factory(Category::class)->make()->toArray();
        $response = $this->put(route('api.pages.categories.update', $category->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:category
     * @group  feature:api:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_soft_delete_a_category()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.pages.categories.destroy', $category->getKey()));
        $category = $this->service->withTrashed()->find($category->getKey());

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:category
     * @group  feature:api:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_soft_delete_multiple_categories()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $categories = factory(Category::class, 3)->create();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(route('api.pages.categories.destroy', 'null'), $attributes);
        $categories = $this->service->onlyTrashed();

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:category
     * @group  feature:api:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_the_paginated_list_of_all_trashed_categories()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $categories = factory(Category::class, 2)->create();
        $categories->each->delete();

        // Actions
        $response = $this->get(route('api.pages.categories.trashed'));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:category
     * @group  feature:api:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_restore_destroyed_categories()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 3)->create()->random();
        $category->delete();

        // Actions
        $response = $this->patch(route('api.pages.categories.restore', $category->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:category
     * @group  feature:api:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_restore_multiple_destroyed_categories()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $categories = factory(Category::class, 3)->create();
        $categories->each->delete();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->patch(route('api.pages.categories.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:category
     * @group  feature:api:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_permanently_delete_a_category()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.pages.categories.delete', $category->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:category
     * @group  feature:api:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_permanently_delete_multiple_categories()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $categories = factory(Category::class, 3)->create();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(route('api.pages.categories.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
    }
}
