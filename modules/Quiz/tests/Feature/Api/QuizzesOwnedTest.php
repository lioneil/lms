<?php

namespace Quiz\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Quiz\Models\Form;
use Quiz\Models\Quiz;
use Quiz\Services\QuizServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithForeignKeys;
use Tests\WithPermissionsPolicy;

/**
 * @package Quiz\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class QuizzesOwnedTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy, WithForeignKeys;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(QuizServiceInterface::class);

    }

     /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:quiz
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_quizzes()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['quizzes.index']), ['quizzes.index']);
        $this->withPermissionsPolicy();

        $quiz = factory(Quiz::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $response = $this->get(route('api.quizzes.index',));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'title','subtitle','description',
                    'slug','url','method','type',
                    'metadata','template_id','user_id','user','template'
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:quizzes
     * @return void
     */
    public function a_user_can_store_a_quiz_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['quizzes.store']), ['quizzes.store']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Quiz::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('api.quizzes.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:quiz
     * @return void
     */
    public function a_user_can_only_retrieve_an_owned_single_quiz()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['quizzes.show']), ['quizzes.show']);
        $this->withPermissionsPolicy();

        $quiz = factory(Quiz::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('api.quizzes.show', $quiz->getKey()));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'title','subtitle','description',
                    'slug','url','method','type',
                    'metadata','template_id','user_id','user','template'
                ],
            ]);
    }

     /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:quiz
     * @return void
     */
    public function a_user_can_only_update_an_owned_quiz()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['quizzes.update']), ['quizzes.update']);
        $this->withPermissionsPolicy();

        $quiz = factory(Quiz::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = factory(Quiz::class)->make()->toArray();

        $response = $this->put(route('api.quizzes.update', $quiz->getKey()), $attributes);
        $quiz = $this->service->find($quiz->getKey());
        // dd($attributes->toArray(),$quiz->toArray());
        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($quiz->getTable(), collect($attributes)->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:quiz
     * @return void
     */
    public function a_user_can_only_soft_delete_an_owned_quiz()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['quizzes.destroy']), ['quizzes.destroy']);
        $this->withPermissionsPolicy();

        $quiz = factory(Quiz::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.quizzes.destroy', $quiz->getKey()));
        $quiz = $this->service->withTrashed()->find($quiz->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($quiz->getTable(), $quiz->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:quiz
     * @return void
     */
    public function a_user_can_only_soft_delete_multiple_owned_quizzes()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['quizzes.destroy']), ['quizzes.destroy']);
        $this->withPermissionsPolicy();

        $quizzes = factory(Quiz::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $quizzes->pluck('id')->toArray()];
        $response = $this->delete(route('api.quizzes.destroy', 'null'), $attributes);
        $quizzes = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $quizzes->each(function ($quiz) {
            $this->assertSoftDeleted($quiz->getTable(), $quiz->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:material
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_trashed_quizzes()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['quizzes.trashed']), ['quizzes.trashed']);
        $this->withPermissionsPolicy();

        $quizzes = factory(Quiz::class, 2)->create(['user_id' => $user->getKey()]);
        $quizzes->each->delete();

        // Actions
        $response = $this->get(route('api.quizzes.trashed'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'title','subtitle','description',
                    'slug','url','method','type',
                    'metadata','template_id','user_id','user','template'
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:material
     * @return void
     */
    public function a_user_can_only_restore_owned_destroyed_quiz()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['quizzes.restore']), ['quizzes.restore']);
        $this->withPermissionsPolicy();

        $quiz = factory(Quiz::class, 3)->create(['user_id' => $user->getKey()])->random();
        $quiz->delete();

        // Actions
        $response = $this->patch(route('api.quizzes.restore', $quiz->getKey()));
        $quiz = $this->service->find($quiz->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($quiz->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:quizzes
     * @return void
     */
    public function a_user_can_only_restore_multiple_owned_destroyed_quizzes()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['quizzes.restore']), ['quizzes.restore']);
        $this->withPermissionsPolicy();

        $quizzes = factory(Quiz::class, 3)->create(['user_id' => $user->getKey()]);
        $quizzes->each->delete();

        // Actions
        $attributes = ['id' => $quizzes->pluck('id')->toArray()];
        $response = $this->patch(route('api.quizzes.restore'), $attributes);
        $quizzes = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $quizzes->each(function ($quiz) {
            $this->assertFalse($quiz->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:quiz
     * @return void
     */
    public function a_user_can_only_permanently_delete_multiple_owned_quizzes()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['quizzes.delete']), ['quizzes.delete']);
        $this->withPermissionsPolicy();

        $quizzes = factory(Quiz::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $quizzes->pluck('id')->toArray()];
        $response = $this->delete(route('api.quizzes.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $quizzes->each(function ($quiz) {
            $this->assertDatabaseMissing($quiz->getTable(), $quiz->toArray());
        });
    }
}
