<?php

namespace Forum\Feature\Api;

use Forum\Services\CategoryServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Taxonomy\Models\Category;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Forum\Feature\Api
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
     * @group  feature:api:thread:category
     * @group  feature:api:thread:category:forbidden
     * @return void
     */
    public function an_upermitted_user_cannot_retrieve_the_paginated_list_of_all_categories()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['categories.index']), ['categories.index']);
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->post(route('api.threads.categories.index'));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread:category
     * @group  feature:api:thread:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_update_a_category()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['categories.update']), ['categories.update']);
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 2)->create()->random();

        // Actions
        $attributes = factory(Category::class)->make()->toArray();
        $response = $this->put(route('api.threads.categories.update', $category->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread:category
     * @group  feature:api:thread:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_soft_delete_a_category()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['categories.destroy']), ['categories.destroy']);
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.threads.categories.destroy', $category->getKey()));
        $category = $this->service->withTrashed()->find($category->getKey());

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread:category
     * @group  feature:api:thread:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_soft_delete_multiple_categories()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['categories.destroy']), ['categories.destroy']);
        $this->withPermissionsPolicy();
        $categories = factory(Category::class, 3)->create();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(route('api.threads.categories.destroy', 'null'), $attributes);
        $categories = $this->service->onlyTrashed();

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread:category
     * @group  feature:api:thread:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_restore_destroyed_categories()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['categories.restore']), ['categories.restore']);
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 3)->create()->random();
        $category->delete();

        // Actions
        $response = $this->patch(route('api.threads.categories.restore', $category->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread:category
     * @group  feature:api:thread:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_restore_multiple_destroyed_categories()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['categories.restore']), ['categories.restore']);
        $this->withPermissionsPolicy();
        $categories = factory(Category::class, 3)->create();
        $categories->each->delete();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->patch(route('api.threads.categories.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread:category
     * @group  feature:api:thread:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_permanently_delete_a_category()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['categories.delete']), ['categories.delete']);
        $this->withPermissionsPolicy();
        $category = factory(Category::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.threads.categories.delete', $category->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread:category
     * @group  feature:api:thread:category:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_permanently_delete_multiple_categories()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['categories.delete']), ['categories.delete']);
        $this->withPermissionsPolicy();
        $categories = factory(Category::class, 3)->create();

        // Actions
        $attributes = ['id' => $categories->pluck('id')->toArray()];
        $response = $this->delete(route('api.threads.categories.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
    }
}
