<?php

namespace Comment\Unit\Services;

use Comment\Models\Comment;
use Comment\Services\CommentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use User\Models\User;

/**
 * @package Comment\Unit\Services
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CommentServiceReactionsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CommentServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:comment
     * @group  unit:comment:service
     * @return void
     */
    public function it_can_like_a_given_comment()
    {
        // Arrangements
        $comment = factory(Comment::class)->create();
        $user = factory(User::class)->create();
        $anotherUser = factory(User::class)->create();

        // Actions
        $comment->like($user);

        // Assertions
        $this->assertTrue($comment->likedBy($user));
        $this->assertFalse($comment->likedBy($anotherUser));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:comment
     * @group  unit:comment:service
     * @return void
     */
    public function it_can_dislike_a_given_comment()
    {
        // Arrangements
        $comment = factory(Comment::class)->create();
        $user = factory(User::class)->create();
        $anotherUser = factory(User::class)->create();

        // Actions
        $comment->dislike($user);

        // Assertions
        $this->assertTrue($comment->dislikedBy($user));
        $this->assertFalse($comment->dislikedBy($anotherUser));
    }
}
