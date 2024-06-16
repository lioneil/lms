<?php

namespace Forum\Feature\Api;

use Comment\Services\ReactionServiceInterface;
use Forum\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithForeignKeys;
use Tests\WithPermissionsPolicy;

/**
 * @package Forum\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ThreadReactionsTest extends TestCase
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
     * @group  feature:api:thread:reaction
     * @return void
     */
    public function a_user_can_like_a_thread()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.like']), ['threads.like']);
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class)->create();

        // Actions
        $attributes = ['user_id' => $user->getKey()];
        $response = $this->post(route('api.threads.like', $thread->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $thread->likedBy($user);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:thread:reaction
     * @return void
     */
    public function a_user_can_dislike_a_thread()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['threads.dislike']), ['threads.dislike']);
        $this->withPermissionsPolicy();

        $thread = factory(Thread::class)->create();

        // Actions
        $attributes = ['user_id' => $user->getKey()];
        $response = $this->post(route('api.threads.dislike', $thread->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $thread->dislikedBy($user);
    }
}
