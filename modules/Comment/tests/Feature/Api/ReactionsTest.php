<?php

namespace Comment\Feature\Api;

use Comment\Models\Comment;
use Comment\Services\ReactionServiceInterface;
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
class ReactionsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy, WithForeignKeys;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ReactionServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:reaction
     * @return void
     */
    public function a_user_can_like_a_comment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.like']), ['comments.like']);
        $this->withPermissionsPolicy();

        $comment = factory(Comment::class)->create();
        $comment->approve();

        // Actions
        $attributes = ['user_id' => $user->getKey()];
        $response = $this->post(route('api.comments.like', $comment->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $comment->likedBy($user);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:reaction
     * @return void
     */
    public function a_user_can_dislike_a_comment()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($user = $this->asNonSuperAdmin(['comments.dislike']), ['comments.dislike']);
        $this->withPermissionsPolicy();

        $comment = factory(Comment::class)->create();
        $comment->approve();

        // Actions
        $attributes = ['user_id' => $user->getKey()];
        $response = $this->post(route('api.comments.dislike', $comment->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $comment->dislikedBy($user);
    }
}
