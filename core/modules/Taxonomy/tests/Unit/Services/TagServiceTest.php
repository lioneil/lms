<?php

namespace Taxonomy\Unit\Services;

use Taxonomy\Services\TagServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Taxonomy\Models\Tag;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Taxonomy\Unit\Services
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TagServiceTest extends TestCase
{
    use ActingAsUser, DatabaseMigrations, RefreshDatabase, WithFaker, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(TagServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:tag
     * @group  service
     * @group  service:tag
     * @return void
     */
    public function it_can_return_a_paginated_list_of_tag()
    {
        // Arrangements
        $tag = factory(Tag::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:tag
     * @group  service
     * @group  service:tag
     * @return void
     */
    public function it_can_find_and_return_an_existing_tag()
    {
        // Arrangements
        $expected = factory(Tag::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Tag::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:tag
     * @group  service
     * @group  service:tag
     * @return void
     */
    public function it_will_abort_to_404_when_a_tag_does_not_exist()
    {
        // Arrangements
        factory(Tag::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:tag
     * @group  service
     * @group  service:tag
     * @return void
     */
    public function it_can_update_an_existing_tag()
    {
       // Arrangements
       $tag = factory(Tag::class)->create();

       // Actions
       $attributes = ['name' => $this->faker->unique()->sentence()];
       $actual = $this->service->update($tag->getKey(), $attributes);

       // Assertions
       $this->assertDatabaseHas($this->service->getTable(), $attributes);
       $this->assertTrue($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:tag
     * @group  service
     * @group  service:tag
     * @return void
     */
    public function it_can_store_a_tag_to_database()
    {
        // Arrangements
        $attributes = factory(Tag::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:tag
     * @group  service
     * @group  service:tag
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules();

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('name', $rules);
        $this->assertEquals('required|max:255', $rules['name']);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:tag
     * @group  service
     * @group  service:tag
     * @return void
     */
    public function it_should_return_an_array_of_messages()
    {
        // Arrangements
        $messages = $this->service->messages();

        // Assertions
        $this->assertIsArray($messages);
    }
}
