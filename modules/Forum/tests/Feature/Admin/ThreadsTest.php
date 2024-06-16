<?php

namespace Forum\Feature\Admin;

use Forum\Models\Thread;
use Forum\Services\ThreadServiceInterface;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Forum\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ThreadsTest extends TestCase
{
    use ActingAsUser, DatabaseMigrations, RefreshDatabase, WithFaker, WithPermissionsPolicy;

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
     * @group  feature:thread
     * @group  threads.index
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $threads = factory(Thread::class, 5)->create();

        // Actions
        $response = $this->get(route('threads.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('forum::admin.index')
                 ->assertSeeText(trans('Add Threads'))
                 ->assertSeeText(trans('All Threads'))
                 ->assertSeeTextInOrder($threads->pluck('title')->toArray())
                 ->assertSeeTextInOrder($threads->pluck('slug')->toArray())
                 ->assertSeeTextInOrder($threads->pluck('body')->toArray())
                 ->assertSeeTextInOrder($threads->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.trashed
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_trashed_threads()
    {
        // Arrangements
        $this->actingAs($usre = $this->superAdmin);
        $threads = factory(Thread::class, 5)->create();
        $threads->each->delete();

        // Actions
        $response = $this->get(route('threads.trashed'));

        // Assertions
        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('forum::admin.trashed')
                 ->assertSeeText(trans('Back to Threads'))
                 ->assertSeeText(trans('Archived Threads'))
                 ->assertSeeTextInOrder($threads->pluck('title')->toArray())
                 ->assertSeeTextInOrder($threads->pluck('slug')->toArray())
                 ->assertSeeTextInOrder($threads->pluck('body')->toArray())
                 ->assertSeeTextInOrder($threads->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Restore'), trans('Remove Permanently')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.show
     * @return void
     */
    public function a_super_user_can_visit_a_thread_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $thread = factory(Thread::class, 5)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('threads.show', $thread->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('forum::admin.show')
                 ->assertSeeText($thread->title);
        $this->assertEquals($thread->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.create
     * @return void
     */
    public function a_super_user_can_visit_the_create_thread_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);

        // Actions
        $response = $this->get(route('threads.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('forum::admin.create')
                 ->assertSeeText(trans('Create Thread'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('Slug'))
                 ->assertSeeText(trans('Body'))
                 ->assertSeeText(trans('Save Thread'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.edit
     * @return void
     */
    public function a_super_user_can_visit_the_edit_thread_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $thread = factory(Thread::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('threads.edit', $thread->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewHas('resource')
                 ->assertViewIs('forum::admin.edit')
                 ->assertSeeText(trans('Edit Thread'))
                 ->assertSeeText($thread->title)
                 ->assertSeeText($thread->slug)
                 ->assertSeeText($thread->body)
                 ->assertSeeText(trans('Update Thread'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.restore
     * @return void
     */
    public function a_super_user_can_restore_an_thread()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $thread = factory(Thread::class, 3)->create()->random();
        $thread->delete();

        // Actions
        $response = $this->patch(route('threads.restore', $thread->getKey()), [], ['HTTP_REFERER' => route('threads.trashed')]);
        $thread = $this->service->find($thread->getKey());

        // Assertions
        $response->assertRedirect(route('threads.trashed'));
        $this->assertFalse($thread->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.restore
     * @return void
     */
    public function a_super_user_can_restore_multiple_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $threads = factory(Thread::class, 3)->create();
        $threads->each->delete();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->patch(route('threads.restore'), $attributes, ['HTTP_REFERER' => route('threads.trashed')]);
        $threads = $this->service->whereIn('id', $threads->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('threads.trashed'));
        $threads->each(function ($thread) {
            $this->assertFalse($thread->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.update
     * @return void
     */
    public function a_super_user_can_update_an_thread()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $thread = factory(Thread::class, 3)->create()->random();

        // Actions
        $attributes = factory(thread::class)->make()->toArray();
        $response = $this->put(route('threads.update', $thread->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('threads.show', $thread->getKey()));
        $this->assertDatabaseHas($thread->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_an_thread()
    {
        // Arrangement
        $this->actingAs($user = $this->superAdmin);
        $threads = factory(Thread::class, 3)->create()->random();

        // Actions
        $response = $this->delete(
            route('threads.destroy', $threads->getKey()), [], ['HTTP_REFERER' => route('threads.index')]
        );

        // Assertions
        $response->assertRedirect(route('threads.index'));
        $this->assertSoftDeleted($threads->getTable(), $threads->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_multiple_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $threads = factory(Thread::class, 3)->create();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->delete(route('threads.destroy', $single = 'false'), $attributes);
        $threads = $this->service->withTrashed()->whereIn('id', $threads->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('threads.index'));
        $threads->each(function ($thread) {
            $this->assertSoftDeleted($thread->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_an_thread()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $thread = factory(Thread::class, 3)->create()->random();
        $thread->delete();

        // Actions
        $response = $this->delete(
            route('threads.delete', $thread->getKey()), [],
            ['HTTP_REFERER' => route('threads.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('threads.trashed'));
        $this->assertDatabaseMissing($thread->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_multiple_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $threads = factory(Thread::class, 3)->create();
        $threads->each->delete();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->delete(
            route('threads.delete'), $attributes, ['HTTP_REFERER' => route('threads.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('threads.trashed'));
        $threads->each(function ($thread) {
            $this->assertDatabaseMissing($thread->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.store
     * @return void
     */
    public function a_super_user_can_store_an_thread_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);

        // Actions
        $attributes = factory(Thread::class)->make([
            'user_id' => $user->getKey(),
        ])->toArray();
        $response = $this->post(route('threads.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('threads.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.preview
     * @group  user:threads.preview
     * @return void
     */
    public function a_super_user_can_only_preview_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $thread = factory(Thread::class, 3)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('threads.preview', $thread->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('forum::admin.preview')
                 ->assertSeeText(trans('Preview Thread'))
                 ->assertSeeTextInOrder([trans('Continue Editing'), trans('Move to Trash')])
                 ->assertSeeText($thread->title)
                 ->assertSeeText($thread->slug)
                 ->assertSeeText($thread->body);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.preview
     * @group  user:threads.preview
     * @return void
     */
    public function a_super_user_can_preview_others_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $this->WithPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('threads.preview', $thread->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.index
     * @group  user::threads.index
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.index', 'threads.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Thread::class, 2)->create();
        $threads = factory(Thread::class, 2)->create(['user_id' => $user->getKey()]);

        // Actions
        $response = $this->get(route('threads.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('forum::admin.index')
                 ->assertSeeText(trans('All Threads'))
                 ->assertSeeTextInOrder($threads->pluck('title')->toArray())
                 ->assertSeeTextInOrder($threads->pluck('slug')->toArray())
                 ->assertSeeTextInOrder($threads->pluck('body')->toArray())
                 ->assertSeeTextInOrder($threads->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->title)
                 ->assertDontSeeText($restricted->random()->slug)
                 ->assertDontSeeText($restricted->random()->body);
        $this->assertSame($threads->random()->author, $user->displayname);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.trashed
     * @group  user::threads.trashed
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_trashed_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.trashed', 'threads.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Thread::class, 2)->create();
        $restricted->each->delete();
        $threads = factory(Thread::class, 2)->create(['user_id' => $user->getKey()]);
        $threads->each->delete();

        // Actions
        $response = $this->get(route('threads.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('forum::admin.trashed')
                 ->assertSeeText(trans('Back to Thread'))
                 ->assertSeeText(trans('Archived Thread'))
                 ->assertSeeTextInOrder($threads->pluck('title')->toArray())
                 ->assertSeeTextInOrder($threads->pluck('slug')->toArray())
                 ->assertSeeTextInOrder($threads->pluck('body')->toArray())
                 ->assertSeeTextInOrder($threads->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->title)
                 ->assertDontSeeText($restricted->random()->slug)
                 ->assertDontSeeText($restricted->random()->body)
                 ->assertDontSeeText($restricted->random()->author);
        $this->assertSame($threads->random()->author, $user->displayname);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.owned
     * @group  user:threads.owned
     * @return void
     */
    public function a_user_can_visit_owned_threads_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.edit', 'threads.show', 'threads.owned', 'threads.destroy']));
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('threads.show', $thread->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('forum::admin.show')
                 ->assertSeeText($thread->title)
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
        $this->assertEquals($thread->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.show
     * @group  user:threads.show
     * @return void
     */
    public function a_user_can_visit_any_thread_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.edit', 'threads.show', 'threads.owned', 'threads.destroy']));
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 4)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('threads.show', $thread->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('forum::admin.show')
                 ->assertSeeText($thread->title);
        $this->assertEquals($thread->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.create
     * @group  user:threads.create
     * @return void
     */
    public function a_user_can_visit_the_create_thread_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.create']));
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->get(route('threads.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('forum::admin.create')
                 ->assertSeeText(trans('Create Thread'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('Slug'))
                 ->assertSeeText(trans('Body'))
                 ->assertSeeText(trans('Save Thread'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.store
     * @group  user:threads.store
     * @return void
     */
    public function a_user_can_store_an_thread_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.create', 'threads.store']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Thread::class)->make([
            'user_id' => $user->getKey(),
        ])->toArray();
        $response = $this->post(route('threads.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('threads.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.show
     * @group  user:threads.show
     * @return void
     */
    public function a_user_cannot_edit_others_pages_on_the_show_thread_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'threads.edit', 'threads.show', 'threads.owned', 'threads.destroy'
        ]));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'threads.edit', 'threads.show', 'threads.owned', 'threads.destroy'
        ]);

        $thread = factory(Thread::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('threads.show', $thread->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('forum::admin.show')
                 ->assertDontSeeText(trans('Edit'))
                 ->assertDontSeeText(trans('Move to Trash'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.edit
     * @group  user:threads.edit
     * @return void
     */
    public function a_user_can_only_visit_their_owned_edit_thread_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.edit', 'threads.update']));
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('threads.edit', $thread->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.edit
     * @group  user:threads.edit
     * @return void
     */
    public function a_user_cannot_visit_others_edit_thread_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.edit', 'threads.update', 'threads.owned']));
        $thread = factory(Thread::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('threads.edit', $thread->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.update
     * @group  user:threads.update
     * @return void
     */
    public function a_user_can_only_update_their_owned_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.owned', 'threads.update']));
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = factory(Thread::class)->make()->toArray();
        $response = $this->put(route('threads.update', $thread->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('threads.show', $thread->getKey()));
        $this->assertDatabaseHas($thread->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.update
     * @group  user:threads.update
     * @return void
     */
    public function a_user_cannot_update_others_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.owned', 'threads.update']));
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create()->random();

        // Actions
        $attributes = ['title' => $this->faker->words($count = 5, $asText = true)];
        $response = $this->put(route('threads.update', $thread->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($thread->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.restore
     * @group  user:threads.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_thread()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.owned', 'threads.restore']));
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create([
            'user_id' => $user->getKey(),
        ])->random();
        $thread->delete();

        // Actions
        $response = $this->patch(
            route('threads.restore', $thread->getKey()), [], ['HTTP_REFERER' => route('threads.trashed')]
        );
        $thread = $this->service->find($thread->getKey());

        // Assertions
        $response->assertRedirect(route('threads.trashed'));
        $this->assertFalse($thread->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.restore
     * @group  user:threads.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_multiple_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.owned', 'threads.restore']));
        $this->withPermissionsPolicy();

        $threads = factory(Thread::class, 3)->create([
            'user_id' => $user->getKey(),
        ]);
        $threads->each->delete();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->patch(
            route('threads.restore'), $attributes, ['HTTP_REFERER' => route('threads.trashed')]
        );
        $threads = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertRedirect(route('threads.trashed'));
        $threads->each(function ($thread) {
            $this->assertFalse($thread->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.restore
     * @group  user:threads.restore
     * @return void
     */
    public function a_user_cannot_restore_others_thread()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.owned', 'threads.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['threads.owned', 'threads.restore']);
        $thread = factory(Thread::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();
        $thread->delete();

        // Actions
        $response = $this->patch(
            route('threads.restore', $thread->getKey()), [], ['HTTP_REFERER' => route('threads.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($thread->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.restore
     * @group  user:threads.restore
     * @return void
     */
    public function a_user_cannot_restore_others_multiple_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.owned', 'threads.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['threads.owned', 'threads.restore']);
        $threads = factory(Thread::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ]);
        $threads->each->delete();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->patch(
            route('threads.restore'), $attributes, ['HTTP_REFERER' => route('threads.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $threads->each(function ($thread) {
            $this->assertDatabaseHas($thread->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.destroy
     * @group  user:threads.destroy
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_thread()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.destroy', 'threads.owned']));
        $this->withPermissionsPolicy();
        $thread = factory(Thread::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('threads.destroy', $thread->getKey()));

        // Assertions
        $response->assertRedirect(route('threads.index'));
        $this->assertSoftDeleted($thread->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.destroy
     * @group  user:threads.destroy
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.index', 'threads.destroy', 'threads.owned']));
        $this->withPermissionsPolicy();
        $threads = factory(Thread::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->delete(
            route('threads.destroy', '@null'), $attributes, ['HTTP_REFERER' => route('threads.index')]
        );
        $threads = $this->service->withTrashed()->whereIn('id', $threads->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('threads.index'));
        $threads->each(function ($thread) {
            $this->assertSoftDeleted($thread->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.destroy
     * @group  user:threads.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_thread()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.destroy', 'threads.owned']));
        $this->withPermissionsPolicy();
        $thread = factory(Thread::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('threads.destroy', $thread->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($thread->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.destroy
     * @group  user:threads.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_multiple_others_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.trashed', 'threads.destroy', 'threads.owned']));
        $this->withPermissionsPolicy();
        $threads = factory(Thread::class, 3)->create();
        $threads->each->delete();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->delete(
            route('threads.destroy', '@null'), $attributes, ['HTTP_REFERER' => route('threads.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $threads->each(function ($thread) {
            $this->assertDatabaseHas($thread->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.destroy
     * @group  user:threads.destroy
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_thread()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.trashed', 'threads.delete', 'threads.owned']));
        $this->withPermissionsPolicy();
        $thread = factory(Thread::class, 3)->create([
            'user_id' => $user->getKey(),
        ])->random();
        $thread->delete();

        // Actions
        $response = $this->delete(
            route('threads.delete', $thread->getKey()), [], ['HTTP_REFERER' => route('threads.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('threads.trashed'));
        $this->assertDatabaseMissing($thread->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.delete
     * @group  user:threads.delete
     * @return void
     */
    public function a_user_can_only_multiple_permanetly_delete_owned_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.trashed', 'threads.delete', 'threads.owned']));
        $this->withPermissionsPolicy();
        $threads = factory(Thread::class, 3)->create([
            'user_id' => $user->getKey(),
        ]);
        $threads->each->delete();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->delete(
            route('threads.delete'), $attributes, ['HTTP_REFERER' => route('threads.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('threads.trashed'));
        $threads->each(function ($thread) {
            $this->assertDatabaseMissing($thread->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.delete
     * @group  user:threads.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_thread()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.trashed', 'threads.delete', 'threads.owned']));
        $this->withPermissionsPolicy();
        $thread = factory(Thread::class, 3)->create()->random();
        $thread->delete();

        // Actions
        $response = $this->delete(
            route('threads.delete', $thread->getKey()), [], ['HTTP_REFERER' => route('threads.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($thread->getTable(), $thread->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.delete
     * @group  user:threads.delete
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_others_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.trashed', 'threads.delete', 'threads.owned']));
        $this->withPermissionsPolicy();
        $threads = factory(thread::class, 3)->create();
        $threads->each->delete();

        // Actions
        $attributes = ['id' => $threads->pluck('id')->toArray()];
        $response = $this->delete(
            route('threads.delete'), $attributes, ['HTTP_REFERER' => route('threads.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $threads->each(function ($thread) {
            $this->assertDatabaseHas($thread->getTable(), $thread->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.preview
     * @group  user:threads.preview
     * @return void
     */
    public function a_user_can_only_preview_owned_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.preview', 'threads.owned']));
        $this->WithPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('threads.preview', $thread->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('forum::admin.preview')
                 ->assertSeeText(trans('Preview Thread'))
                 ->assertSeeText($thread->title)
                 ->assertSeeText($thread->slug)
                 ->assertSeeText($thread->body);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:thread
     * @group  threads.preview
     * @group  user:threads.preview
     * @return void
     */
    public function a_user_cannot_preview_others_threads()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['threads.preview', 'threads.owned']));
        $this->WithPermissionsPolicy();

        $thread = factory(Thread::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('threads.preview', $thread->getKey()));

        // Assertions
        $response->assertForbidden();
    }
}
