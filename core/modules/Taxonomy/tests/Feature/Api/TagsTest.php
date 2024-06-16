<?php

namespace Taxonomy\Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Taxonomy\Models\Tag;
use Taxonomy\Services\TagServiceInterface;
use Tests\ActingAsTag;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Taxonomy\Tests\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TagsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(TagServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:tag
     * @return void
     */
    public function a_user_can_retrieve_the_paginated_list_of_all_tags()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['tags.index']), ['tags.index']);
        $this->withPermissionsPolicy();

        $tags = factory(Tag::class, 5)->create(['type' => 'tag']);

        // Actions
        $response = $this->get(route('api.tags.index'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJson(['data' => [['type' => 'tag']]])
            ->assertJsonStructure([
                'data' => [[
                    'name', 'icon', 'type',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:tag
     * @return void
     */
    public function a_user_can_store_a_tag_to_database()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($this->asNonSuperAdmin(['tags.store']), ['tags.store']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Tag::class)->make()->toArray();
        $response = $this->post(route('api.tags.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:tag
     * @return void
     */
    public function a_user_can_retrieve_a_single_tag()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['tags.show']), ['tags.show']);
        $this->withPermissionsPolicy();
        $tag = factory(Tag::class, 2)->create(['type' => 'tag'])->random();

        // Actions
        $response = $this->get(route('api.tags.show', $tag->getKey()));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJson(['data' => ['type' => 'tag']])
            ->assertJsonStructure([
                'data' => [
                    'name', 'icon', 'type',
                ],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:tag
     * @return void
     */
    public function a_user_can_update_a_tag()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['tags.update']), ['tags.update']);
        $this->withPermissionsPolicy();
        $original = factory(Tag::class, 3)->create()->random();

        // Actions
        $attributes = factory(Tag::class)->make()->toArray();
        $response = $this->put(route('api.tags.update', $original->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:tag
     * @return void
     */
    public function a_user_can_destroy_a_tag()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['tags.destroy']), ['tags.destroy']);
        $this->withPermissionsPolicy();
        $tag = factory(Tag::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.tags.destroy', $tag->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($tag->getTable(), $tag->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:tag
     * @return void
     */
    public function a_user_can_destroy_multiple_tags()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['tags.destroy']), ['tags.destroy']);
        $this->withPermissionsPolicy();
        $tags = factory(Tag::class, 3)->create();

        // Actions
        $attributes = ['id' => $tags->pluck('id')->toArray()];
        $response = $this->delete(route('api.tags.destroy', 'null'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $tags->each(function ($tag) {
            $this->assertDatabaseMissing($tag->getTable(), $tag->toArray());
        });
    }
}
