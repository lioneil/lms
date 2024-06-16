<?php

namespace Forum\Feature\Api;

use Forum\Models\Thread;
use Forum\Services\ThreadServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Forum\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ThreadsTest extends TestCase
{
    use ActingAsUser, RefreshDatabase, WithFaker, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ThreadServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_threads()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.index', 'threads.owned']), ['threads.index']);
        $this->withPermissionsPolicy();

        $threads = factory(Thread::class, 3)->create(['user_id' => $user->getKey(),
        ]);

        // Actions
        $response = $this->get(route('api.threads.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [[
                    'user_id' => $user->getKey(),
                ]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'title', 'slug', 'body',
                        'type', 'user_id', 'category_id',
                        'created', 'modified', 'deleted',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_trashed_threads()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.trashed', 'threads.owned']), ['threads.trashed']);
        $this->withPermissionsPolicy();

        $threads = factory(Thread::class, 2)->create([
            'user_id' => $user->getKey(),

        ]);
        $threads->each->delete();

        // Actions
        $response = $this->get(route('api.threads.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [[
                    'user_id' => $user->getKey(),

                 ]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'title',
                        'user_id',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_can_visit_their_owned_thread_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.show', 'threads.owned']), ['threads.show']);
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 2)->create([
            'user_id' => $user->getKey(),

        ])->random();

        // Actions
        $response = $this->get(route('api.threads.show', $thread->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'user_id' => $user->getKey(),

                 ]])
                 ->assertJsonStructure([
                    'data' => [
                        'title',
                        'user_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread.test
     * @return void
     */
    public function a_user_can_visit_any_thread_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.show']), ['threads.show']);
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 2)->create([
            'user_id' => $user->getKey(),

        ])->random();

        // Actions
        $response = $this->get(route('api.threads.show', $thread->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'user_id' => $user->getKey(),

                 ]])
                 ->assertJsonStructure([
                    'data' => [
                        'title',
                        'user_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_can_store_a_thread_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.store']), ['threads.store']);
        $this->withPermissionsPolicy();


        // Actions
        $attributes = factory(Thread::class)->make([
            'user_id' => $user->getKey(),

        ])->toArray();
        $response = $this->post(route('api.threads.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_can_only_update_their_owned_threads()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.owned', 'threads.update']), ['threads.update']);
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $attributes = factory(Thread::class)->make()->toArray();
        $response = $this->put(route('api.threads.update', $thread->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($thread->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_cannot_update_threads_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.owned', 'threads.update']), ['threads.update']);
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create()->random();

        // Actions
        $attributes = factory(Thread::class)->make()->toArray();
        $response = $this->put(route('api.threads.update', $thread->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($thread->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_can_only_restore_owned_thread()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.restore', 'threads.owned']), ['threads.restore']);
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create([
            'user_id' => $user->getKey(),

        ])->random();

        // Actions
        $response = $this->patch(route('api.threads.restore', $thread->getKey()));
        $thread = $this->service->find($thread->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($thread->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_can_only_multiple_restore_owned_threads()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.restore', 'threads.owned']), ['threads.restore']);
        $this->withPermissionsPolicy();

        $threads = factory(Thread::class, 3)->create([
            'user_id' => $user->getKey(),

        ])->random();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->patch(route('api.threads.restore'), $attributes);
        $threads = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $threads->each(function ($thread) {
            $this->assertFalse($thread->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_cannot_restore_thread_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.restore', 'threads.owned']), ['threads.restore']);
        $this->withPermissionsPolicy();


        $otherUser = $this->asNonSuperAdmin(['threads.owned', 'threads.restore']);
        $thread = factory(Thread::class, 3)->create([
            'user_id' => $otherUser->getKey(),

        ])->random();

        // Actions
        $response = $this->patch(route('api.threads.restore', $thread->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($thread->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_cannot_multiple_restore_threads_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.restore', 'threads.owned']), ['threads.restore']);
        $this->withPermissionsPolicy();


        $otherUser = $this->asNonSuperAdmin(['threads.owned', 'threads.restore']);
        $threads = factory(Thread::class, 3)->create([
            'user_id' => $otherUser->getKey(),

        ]);

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->patch(route('api.threads.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
        $threads->each(function ($thread) {
            $this->assertDatabaseHas($thread->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_thread()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.destroy', 'threads.owned']), ['threads.destroy']);
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create([
            'user_id' => $user->getKey(),

        ])->random();

        // Actions
        $response = $this->delete(route('api.threads.destroy', $thread->getKey()));
        $thread = $this->service->withTrashed()->find($thread->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($thread->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_threads()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.destroy', 'threads.owned']), ['threads.destroy']);
        $this->withPermissionsPolicy();

        $threads = factory(Thread::class, 3)->create([
            'user_id' => $user->getKey(),

        ])->random();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->delete(route('api.threads.destroy', 'null'), $attributes);
        $threads = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $threads->each(function ($thread) {
            $this->assertSoftDeleted($thread->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_cannot_soft_delete_thread_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.destroy', 'threads.owned']), ['threads.destroy']);
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.threads.destroy', $thread->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($thread->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_cannot_multiple_soft_delete_threads_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.destroy', 'threads.owned']), ['threads.destroy']);
        $this->withPermissionsPolicy();

        $threads = factory(Thread::class, 3)->create();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->delete(route('api.threads.destroy', 'null'), $attributes);
        $threads = $this->service->withTrashed()->whereIn('id', $threads->pluck('id')->toArray())->get();

        // Assertions
        $response->assertForbidden();
        $threads->each(function ($thread) {
            $this->assertDatabaseHas($thread->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_thread()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.delete', 'threads.owned']), ['threads.delete']);
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 2)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->delete(route('api.threads.delete', $thread->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($thread->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_threads()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.delete', 'threads.owned']), ['threads.delete']);
        $this->withPermissionsPolicy();

        $threads = factory(Thread::class, 3)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->delete(route('api.threads.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $threads->each(function ($thread) {
            $this->assertDatabaseMissing($thread->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_cannot_permanently_delete_thread_owned_by_other_threads()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['threads.delete', 'threads.owned']), ['threads.delete']);
        $this->withPermissionsPolicy();
        $thread = factory(Thread::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.threads.delete', $thread->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($thread->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_threads_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.delete', 'threads.owned']), ['threads.delete']);
        $this->withPermissionsPolicy();

        $threads = factory(Thread::class, 3)->create();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->delete(route('api.threads.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
        $threads->each(function ($thread) {
            $this->assertDatabaseHas($thread->getTable(), $thread->toArray());
        });
    }
}
