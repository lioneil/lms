<?php

namespace Announcement\Unit;

use Announcement\Services\CategoryServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Taxonomy\Models\Category;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Announcement\Unit
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AnnouncementCategoryServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /* Set up the service class*/
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CategoryServiceInterface::class);
    }

    /**
     * Browse
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_return_a_paginated_list_of_announcement_category()
    {
        // Arrangements
        $announcements = factory(Category::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * Read
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_find_and_return_an_existing_announcement_category()
    {
        // Arrangements
        $expected = factory(Category::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Category::class, $actual);
    }

    /**
     * Read
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_will_abort_to_404_when_a_announcement_category_does_not_exist()
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
     * Edit
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_update_an_existing_announcement_category()
    {
         // Arrangements
        $announcement = factory(Category::class)->create();

        // Actions
        $attributes = [
            'name' => $name = $this->faker->unique()->words(10, true),
            'user_id' => factory(User::class)->create()->getKey(),
        ];
        $actual = $this->service->update($announcement->getKey(), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $this->assertTrue($actual);
    }

    /**
     * Add
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_store_a_announcement_category_to_database()
    {
        // Arrangements
        $attributes = factory(Category::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * Rules
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules($id = 1);

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('name', $rules);
        $this->assertEquals('required|max:255', $rules['name']);
    }

    /**
     * Rules
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
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
     * Authorization
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_check_if_user_is_authorized_to_interact_with_announcement_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->withPermissionsPolicy();
        $restricted = factory(Category::class, 3)->create()->random();
        $category = factory(Category::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $restricted = $this->service->authorize($restricted);
        $authorized = $this->service->authorize($category);

        // Assertions
        $this->assertFalse($restricted);
        $this->assertTrue($authorized);
    }

}
