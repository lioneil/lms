<?php

namespace Forum\Feature\Api;

use Comment\Models\Comment;
use Comment\Services\CommentServiceInterface;
use Forum\Models\Thread;
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
class ThreadCommentsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

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
     * @group  feature:api:thread:comment
     * @return void
     */
    public function a_user_can_post_a_comment_to_a_thread()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.store']), ['comments.store']);
        $this->withPermissionsPolicy();

        // Actions
        $thread = factory(Thread::class)->create();
        $attributes = factory(Comment::class)->make([
            'commentable_id' => $thread->getKey(),
            'commentable_type' => Thread::class
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
     * @group  feature:api:thread:comment
     * @return void
     */
    public function a_user_can_edit_their_owned_comment_to_a_thread()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.update']), ['comments.update']);
        $this->withPermissionsPolicy();

        $comment = factory(Comment::class, 2)->create([
            'user_id' => $user->getKey()
        ])->random();
        $comment->approve();

        // Actions
        $thread = factory(Thread::class)->create();
        $attributes = factory(Comment::class)->make([
            'user_id' => $user->getKey(),
            'commentable_id' => $thread->getKey(),
            'commentable_type' => Thread::class
        ])->toArray();
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
     * @group  feature:api:thread:comment
     * @return void
     */
    public function a_user_can_delete_owned_comment_to_a_thread()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.delete', 'comments.owned']), ['comments.delete']);
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class)->create();
        $comment = factory(Comment::class, 2)->create([
            'user_id' => $user->getKey(),
            'commentable_id' => $thread->getKey(),
            'commentable_type' => Thread::class
        ])->random();
        $comment->approve();

        // Actions
        $response = $this->delete(route('api.comments.delete', $comment->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($comment->getTable(), $comment->toArray());
    }
}
