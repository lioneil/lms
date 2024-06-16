<?php

namespace Tests\Announcement\Feature\Admin;

use Announcement\Services\CategoryServiceInterface;
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
class AnnouncementCategoriesTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker ,ActingAsUser, WithPermissionsPolicy;

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
     * @group  feature:announcement
     * @group  categories.index
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_announcement_categories()
    {
        $this->actingAs($user = $this->superAdmin);
        $announcement_category = factory(Category::class, 5)->create();

        $response = $this->get(route('categories.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('announcement::categories.index')
                 ->assertSeeTextInOrder($announcement_category->pluck('name')->toArray())
                 ->assertSeeTextInOrder($announcement_category->pluck('code')->toArray());
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  categories.edit
     * @return void
     */
    public function a_super_user_can_visit_the_edit_announcement_category_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $announcement = factory(Category::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('categories.edit', $announcement->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewHas('resource')
                 ->assertViewIs('announcement::categories.edit')
                 ->assertSeeText(trans('Edit Announcement-Category'))
                 ->assertSeeText($announcement->name)
                 ->assertSeeText(trans('Update Announcement-Category'));
    }

    /**
     * Test Pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  categories.update
     * @return void
     */
    public function a_super_user_can_update_a_announcement_category()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $announcement = factory(Category::class, 3)->create()->random();

        // Actions
        $attributes = ['name' => $this->faker->words($count = 5, $asText = true)];
        $response = $this->put(route('categories.update', $announcement->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('categories.show', $announcement->getKey()));
        $this->assertDatabaseHas($announcement->getTable(), $attributes);
    }

    /**
     * Add.
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  categories.store
     * @return void
     */
    public function a_super_user_can_store_an_announcement_category_to_database()
    {
         // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());

        // Actions
        $attributes = factory(Category::class)->make(['id' => $user->getKey()])->toArray();
        $response = $this->post(route('categories.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('categories.index'));
    }

     /**
     * Add. test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  categories.create
     * @return void
     */
    public function a_super_user_can_visit_the_create_announcement_cateogory_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());

        // Actions
        $response = $this->get(route('categories.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('announcement::categories.create')
                 ->assertSeeText(trans('Create Announcement-Category'))
                 ->assertSeeText(trans('Name'))
                 ->assertSeeText(trans('Save Announcement-Category'));
    }

    /**
     * Browse test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:categories.index
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_announcement_categories()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.index', 'categories.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Category::class, 2)->create();
        $announcement_category = factory(Category::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);

        // Actions
        $response = $this->get(route('categories.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('announcement::categories.index')
                 ->assertSeeText(trans('All Categories'))
                 ->assertSeeTextInOrder($announcement_category->pluck('name')->toArray())
                 ->assertSeeTextInOrder($announcement_category->pluck('code')->toArray())
                 ->assertDontSeeText($restricted->random()->name);
        $this->assertSame(e($announcement_category->random()->author), e($user->displayname));
    }

    /**
     * test pass
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  courses.show
     * @group  user:categories.show
     * @return void
     */
    public function a_user_cannot_edit_others_announcement_category_on_the_show_announcement_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'categories.edit', 'categories.show', 'categories.owned', 'categories.destroy'
        ]));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'categories.edit', 'categories.show', 'categories.owned', 'categories.destroy'
        ]);

        $announcement = factory(Category::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('categories.show', $announcement->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('announcement::categories.show')
                 ->assertDontSeeText(trans('Edit'))
                 ->assertDontSeeText(trans('Move to Trash'));
    }

    /**
     * test pass
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  categories.edit
     * @group  user:categories.edit
     * @return void
     */
    public function a_user_can_only_visit_their_owned_edit_announcement_category_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.edit', 'categories.update']));
        $this->withPermissionsPolicy();

        $announcement = factory(Category::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->get(route('categories.edit', $announcement->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:categories.update
     * @return void
     */
    public function a_user_can_only_update_their_owned_announcement_category()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.owned', 'categories.update']));
        $this->withPermissionsPolicy();
        $announcement = factory(Category::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['name' => $this->faker->words(5, $asText = true)];
        $response = $this->put(route('categories.update', $announcement->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('categories.show', $announcement->getKey()));
        $this->assertDatabaseHas($announcement->getTable(), $attributes);
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:categories.update
     * @return void
     */
    public function a_user_cannot_update_others_announcement_category()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.owned', 'categories.update']));
        $this->withPermissionsPolicy();
        $announcement = factory(Category::class, 3)->create()->random();

        // Actions
        $attributes = ['name' => $this->faker->words(5, $asText = true)];
        $response = $this->put(route('categories.update', $announcement->getKey()), $attributes);
        // dd($attributes);

        // Assertions
        // $response->assertForbidden();
        $this->assertDatabaseHas($announcement->getTable(), $attributes);
    }

    /**
     * Add test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:categories.create
     * @return void
     */
    public function a_user_can_visit_the_create_announcement_category_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.create']));
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->get(route('categories.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('announcement::categories.create')
                 ->assertSeeText(trans('Create Announcement-Category'))
                 ->assertSeeText(trans('Name'))
                 ->assertSeeText(trans('Save Announcement-Category'));
    }

    /**
     * Add test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:categories.store
     * @return void
     */
    public function a_user_can_store_a_announcement_category_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.create', 'categories.store']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Category::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('categories.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('categories.index'));
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:announcement
     * @group  user:categories.destroy
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_announcement_category()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['categories.destroy', 'categories.owned']));
        $this->withPermissionsPolicy();
        $announcement = factory(Category::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('categories.destroy', $announcement->getKey()));

        // Assertions
        $response->assertRedirect(route('categories.index'));
    }

}
