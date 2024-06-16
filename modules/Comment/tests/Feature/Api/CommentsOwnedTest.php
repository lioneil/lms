<?php

namespace Comment\Feature\Api;

use Comment\Models\Comment;
use Comment\Services\CommentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithForeignKeys;
use Tests\WithPermissionsPolicy;

/**
 * @package Comment\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CommentsOwnedTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy, WithForeignKeys;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CommentServiceInterface::class);
    }

     /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:comment
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_comments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.index']), ['comments.index']);
        $this->withPermissionsPolicy();

        $comment = factory(Comment::class, 3)->create([
            'user_id' => $user->getKey()
        ])->each->approve();

        // Actions
        $response = $this->get(route('api.comments.index'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'body', 'commentable_id', 'commentable_type',
                    'user_id', 'parent_id', 'approved_at', 'locked_at',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:comments
     * @return void
     */
    public function a_user_can_store_a_comment_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.store']), ['comments.store']);
        $this->withPermissionsPolicy();

        // Actions
        $parent = factory(Comment::class)->create();
        $parent->approve();
        $attributes = factory(Comment::class)->make([
            'user_id' => $user->getKey(),
            'parent_id' => $parent->getKey()
        ])->toArray();
        $response = $this->post(route('api.comments.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:comment
     * @return void
     */
    public function a_user_can_only_retrieve_an_owned_single_comment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.show']), ['comments.show']);
        $this->withPermissionsPolicy();

        $comment = factory(Comment::class, 2)->create([
            'user_id' => $user->getKey()
        ])->random();
        $comment->approve();

        // Actions
        $response = $this->get(route('api.comments.show', $comment->getKey()));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'body', 'commentable_id', 'commentable_type',
                    'user_id', 'parent_id', 'approved_at', 'locked_at',
                ],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:comment
     * @return void
     */
    public function a_user_can_only_update_an_owned_comment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.update']), ['comments.update']);
        $this->withPermissionsPolicy();

        $comment = factory(Comment::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();
        $comment->approve();

        // Actions
        $attributes = factory(Comment::class)->make()->toArray();
        $response = $this->put(route('api.comments.update', $comment->getKey()), $attributes);
        $comment = $this->service->find($comment->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($comment->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:comment
     * @return void
     */
    public function a_user_can_only_soft_delete_an_owned_comment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.destroy']), ['comments.destroy']);
        $this->withPermissionsPolicy();

        $comment = factory(Comment::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();
        $comment->approve();

        // Actions
        $response = $this->delete(route('api.comments.destroy', $comment->getKey()));
        $comment = $this->service->withTrashed()->find($comment->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($comment->getTable(), $comment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:comment
     * @return void
     */
    public function a_user_can_only_soft_delete_multiple_owned_comments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.destroy']), ['comments.destroy']);
        $this->withPermissionsPolicy();

        $comments = factory(Comment::class, 3)->create([
            'user_id' => $user->getKey()
        ])->each->approve();

        // Actions
        $attributes = ['id' => $comments->pluck('id')->toArray()];
        $response = $this->delete(route('api.comments.destroy', 'null'), $attributes);
        $comments = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $comments->each(function ($comment) {
            $this->assertSoftDeleted($comment->getTable(), $comment->toArray());
        });
    }

     /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:comment
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_trashed_comments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.trashed']), ['comments.trashed']);
        $this->withPermissionsPolicy();

        $comments = factory(Comment::class, 2)->create([
            'user_id' => $user->getKey()
        ]);
        $comments->each->approve();
        $comments->each->delete();

        // Actions
        $response = $this->get(route('api.comments.trashed'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'body', 'commentable_id', 'commentable_type',
                    'user_id', 'parent_id', 'approved_at', 'locked_at',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:comment
     * @return void
     */
    public function a_user_can_only_restore_owned_destroyed_comment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.restore']), ['comments.restore']);
        $this->withPermissionsPolicy();

        $comment = factory(Comment::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();
        $comment->approve();
        $comment->delete();

        // Actions
        $response = $this->patch(route('api.comments.restore', $comment->getKey()));
        $comment = $this->service->find($comment->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($comment->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:comments
     * @return void
     */
    public function a_user_can_only_restore_multiple_owned_destroyed_comments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.restore']), ['comments.restore']);
        $this->withPermissionsPolicy();

        $comments = factory(Comment::class, 3)->create(['user_id' => $user->getKey()]);
        $comments->each->approve();
        $comments->each->delete();

        // Actions
        $attributes = ['id' => $comments->pluck('id')->toArray()];
        $response = $this->patch(route('api.comments.restore'), $attributes);
        $comments = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $comments->each(function ($comment) {
            $this->assertFalse($comment->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:comment
     * @return void
     */
    public function a_user_can_only_permanently_delete_multiple_owned_comments()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.delete']), ['comments.delete']);
        $this->withPermissionsPolicy();

        $comments = factory(Comment::class, 3)->create(['user_id' => $user->getKey()]);
        $comments->each->approve();

        // Actions
        $attributes = ['id' => $comments->pluck('id')->toArray()];
        $response = $this->delete(route('api.comments.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $comments->each(function ($comment) {
            $this->assertDatabaseMissing($comment->getTable(), $comment->toArray());
        });
    }
}
