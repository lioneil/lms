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
class ForbiddenThreadCommentsTest extends TestCase
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
     * @group  feature:api:thread:comment:forbidden
     * @return void
     */
    public function a_user_cannot_edit_comments_owned_by_others()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['comments.update']), ['comments.update']);
        $this->withPermissionsPolicy();

        $comment = factory(Comment::class, 2)->create()->random();

        // Actions
        $thread = factory(Thread::class)->create();
        $attributes = factory(Comment::class)->make([
            'commentable_id' => $thread->getKey(),
            'commentable_type' => Thread::class
        ])->toArray();
        $response = $this->put(route('api.comments.update', $comment->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread:comment
     * @group  feature:api:thread:comment:forbidden
     * @return void
     */
    public function a_user_cannot_delete_comments_owned_by_others()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['comments.delete', 'comments.owned']), ['comments.delete']);
        $this->withPermissionsPolicy();
        $thread = factory(Thread::class)->create();
        $comment = factory(Comment::class, 2)->create([
            'commentable_id' => $thread->getKey(),
            'commentable_type' => Thread::class
        ])->random();

        // Actions
        $response = $this->delete(route('api.comments.delete', $comment->getKey()));

        // Assertions
        $response->assertForbidden();
    }
}
