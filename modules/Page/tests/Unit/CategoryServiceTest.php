<?php

namespace Page\Unit;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Page\Services\CategoryServiceInterface;
use Taxonomy\Models\Category;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Page\Unit
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CategoryServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CategoryServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_return_a_paginated_list_of_categories()
    {
        // Arrangements
        $categories = factory(Category::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trahed_categories()
    {
        // Arrangements
        $categories = factory(Category::class, 10)->create();
        $categories->each->delete();

        // Actions
        $actual = $this->service->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_find_and_return_an_existing_category()
    {
        // Arrangements
        $expected = factory(Category::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Category::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_will_abort_to_404_when_a_category_does_not_exist()
    {
        // Arrangements
        factory(Category::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_update_an_existing_category()
    {
       // Arrangements
       $category = factory(Category::class)->create();

       // Actions
       $attributes = [
            'name' => $title = $this->faker->unique()->words(10, true),
            'user_id' => factory(User::class)->create()->getKey(),
       ];
       $actual = $this->service->update($category->getKey(), $attributes);

       // Assertions
       $this->assertDatabaseHas($this->service->getTable(), $attributes);
       $this->assertTrue($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_restore_a_soft_deleted_category()
    {
        // Arrangements
        $category = factory(Category::class)->create();
        $category->delete();

        // Actions
        $actual = $this->service->restore($category->getKey());
        $restored = $this->service->find($category->getKey());

        // Assertions
        $this->assertNull($actual);
        $this->assertNull($restored->deleted_at);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_restore_multiple_soft_deleted_category()
    {
        // Arrangements
        $categories = factory(Category::class, 5)->create();
        $categories->each->delete();

        // Actions
        $actual = $this->service->restore($categories->pluck('id')->toArray());

        // Assertions
        $this->assertNull($actual);
        $categories->each(function ($category) {
            $restored = $this->service->find($category->getKey());
            $this->assertNull($restored->deleted_at);
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_store_a_category_to_database()
    {
        // Arrangements
        $attributes = factory(Category::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_soft_delete_an_existing_category()
    {
        // Arrangements
        $category = factory(Category::class, 3)->create()->random();

        // Actions
        $this->service->destroy($category->getKey());
        $category = $this->service->withTrashed()->find($category->getKey());

        // Assertions
        $this->assertSoftDeleted($this->service->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_soft_delete_multiple_existing_categories()
    {
        // Arrangements
        $categories = factory(Category::class, 3)->create();

        // Actions
        $this->service->destroy($categories->pluck('id')->toArray());
        $categories = $this->service->withTrashed()->whereIn('id', $categories->pluck('id')->toArray())->get();

        // Assertions
        $categories->each(function ($category) {
            $this->assertSoftDeleted($this->service->getTable(), $category->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_category()
    {
        // Arrangements
        $category = factory(Category::class)->create();
        $category->delete();

        // Actions
        $this->service->delete($category->getKey());

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $category->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_permanently_delete_multiple_soft_deleted_categories()
    {
        // Arrangements
        $categories = factory(Category::class, 5)->create();
        $categories->each->delete();

        // Actions
        $this->service->delete($categories->pluck('id')->toArray());

        // Assertions
        $categories->each(function ($category) {
            $this->assertDatabaseMissing($this->service->getTable(), $category->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules($id = 1);

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertEquals('required|max:255', $rules['name']);
        $this->assertEquals('required|numeric', $rules['user_id']);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_should_return_an_array_of_messages()
    {
        // Arrangements
        $messages = $this->service->messages();

        // Assertions
        $this->assertIsArray($messages);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_check_if_user_is_authorized_to_interact_with_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->WithPermissionsPolicy();
        $restricted = factory(Category::class, 3)->create()->random();
        $category = factory(Category::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $restricted = $this->service->authorize($restricted);
        $authorized = $this->service->authorize($category);

        // Assertions
        $this->assertFalse($restricted);
        $this->assertTrue($authorized);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:category
     * @group  service
     * @group  service:category
     * @return void
     */
    public function it_can_check_if_user_has_unrestricted_authorization_to_interact_with_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.unrestricted']));
        $this->WithPermissionsPolicy();
        $category = factory(Category::class, 3)->create()->random();

        // Actions
        $unrestricted = $this->service->authorize($category);

        // Assertions
        $this->assertTrue($unrestricted);
    }
}
