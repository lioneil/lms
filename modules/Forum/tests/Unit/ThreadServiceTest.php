<?php

namespace Forum\Unit;

use Forum\Models\Thread;
use Forum\Services\ThreadServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Forum\Unit
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ThreadServiceTest extends TestCase
{

    use ActingAsUser, DatabaseMigrations, RefreshDatabase, WithFaker, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ThreadServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_can_return_a_paginated_list_of_thread()
    {
        // Arrangements
        $thread = factory(Thread::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_threads()
    {
        // Arrangements
        $threads = factory(Thread::class, 10)->create();
        $threads->each->delete();

        // Actions
        $actual = $this->service->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_can_find_and_return_an_existing_thread()
    {
        // Arrangements
        $expected = factory(Thread::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Thread::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_will_abort_to_404_when_a_thread_does_not_exist()
    {
        // Arrangements
        factory(Thread::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_can_update_an_existing_thread()
    {
       // Arrangements
       $thread = factory(Thread::class)->create();

       // Actions
       $attributes = [
            'title' => $title = $this->faker->unique()->words(10, true),
            'slug' => Str::slug($title),
            'type' => 'thread',
            'user_id' => factory(User::class)->create()->getKey(),
       ];
       $actual = $this->service->update($thread->getKey(), $attributes);

       // Assertions
       $this->assertDatabaseHas($this->service->getTable(), $attributes);
       $this->assertTrue($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_can_restore_a_soft_deleted_thread()
    {
        // Arrangements
        $thread = factory(Thread::class)->create();
        $thread->delete();

        // Actions
        $actual = $this->service->restore($thread->getKey());
        $restored = $this->service->find($thread->getKey());

        // Assertions
        $this->assertNull($actual);
        $this->assertNull($restored->deleted_at);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_can_restore_multiple_soft_deleted_thread()
    {
        // Arrangements
        $threads = factory(Thread::class, 5)->create();
        $threads->each->delete();

        // Actions
        $actual = $this->service->restore($threads->pluck('id')->toArray());

        // Assertions
        $this->assertNull($actual);
        $threads->each(function ($thread) {
            $restored = $this->service->find($thread->getKey());
            $this->assertNull($restored->deleted_at);
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_can_store_a_thread_to_database()
    {
        // Arrangements
        $attributes = factory(Thread::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_can_soft_delete_an_existing_thread()
    {
        // Arrangements
        $thread = factory(Thread::class, 3)->create()->random();

        // Actions
        $this->service->destroy($thread->getKey());
        $thread = $this->service->withTrashed()->find($thread->getKey());

        // Assertions
        $this->assertSoftDeleted($this->service->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_can_soft_delete_multiple_existing_threads()
    {
        // Arrangements
        $threads = factory(Thread::class, 3)->create();

        // Actions
        $this->service->destroy($threads->pluck('id')->toArray());
        $threads = $this->service->withTrashed()->whereIn('id', $threads->pluck('id')->toArray())->get();

        // Assertions
        $threads->each(function ($thread) {
            $this->assertSoftDeleted($this->service->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_thread()
    {
        // Arrangements
        $thread = factory(Thread::class)->create();
        $thread->delete();

        // Actions
        $this->service->delete($thread->getKey());

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_can_permanently_delete_multiple_soft_deleted_threads()
    {
        // Arrangements
        $threads = factory(Thread::class, 5)->create();
        $threads->each->delete();

        // Actions
        $this->service->delete($threads->pluck('id')->toArray());

        // Assertions
        $threads->each(function ($thread) {
            $this->assertDatabaseMissing($this->service->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules($id = 1);

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertArrayHasKey('slug', $rules);
        $this->assertEquals('required|max:255', $rules['title']);
        $this->assertEquals('required|numeric', $rules['user_id']);
        $this->assertEquals([
            'required', 'alpha_dash', Rule::unique($this->service->getTable())->ignore($id)
        ], $rules['slug']);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:thread
     * @group  service
     * @group  service:thread
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
     * @group  unit:thread
     * @group  service
     * @group  service:thread
     * @return void
     */
    public function it_can_check_if_user_is_authorized_to_interact_with_threads()
    {
        // Arrangements\
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->WithPermissionsPolicy();
        $restricted = factory(Thread::class, 3)->create()->random();
        $thread = factory(Thread::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $restricted = $this->service->authorize($restricted);
        $authorized = $this->service->authorize($thread);

        // Assertions
        $this->assertFalse($restricted);
        $this->assertTrue($authorized);
    }
}
