<?php

namespace Tests\Comment\Feature;

use Comment\Models\Comment;
use Comment\Services\CommentServiceInterface;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Comment\Feature
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CommentsTest extends TestCase
{
    use ActingAsUser, DatabaseMigrations, RefreshDatabase, WithFaker, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CommentServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:comment
     * @group  comments.store
     * @return void
     */
    public function a_super_user_can_store_an_comment_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);

        // Actions
        $attributes = factory(Comment::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('comments.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('comments.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:comment
     * @group  comments.delete
     * @group  user:comments.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_an_comment()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $comment = factory(Comment::class, 3)->create()->random();
        $comment->delete();

        // Actions
        $response = $this->delete(
            route('comments.delete', $comment->getKey()), [],
            ['HTTP_REFERER' => route('comments.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('comments.trashed'));
        $this->assertDatabaseMissing($comment->getTable(), $comment->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:comment
     * @group  comments.edit
     * @return void
     */
    public function a_super_user_can_visit_the_edit_comment_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $comment = factory(Comment::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('comments.edit', $comment->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewHas('resource')
                 ->assertViewIs('comment::admin.edit')
                 ->assertSeeText(trans('Edit Comment'))
                 ->assertSeeText($comment->body)
                 ->assertSeeText(trans('Update Comment'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:comment
     * @group  comments.store
     * @group  user:comments.store
     * @return void
     */
    public function a_user_can_store_an_comment_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['comments.create', 'comments.store']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Comment::class)->make([
            'user_id' => $user->getKey(),
        ])->toArray();
        $response = $this->post(route('comments.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('comments.index'));
    }
}
