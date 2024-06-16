<?php

namespace Tests\Quiz\Unit;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Quiz\Models\Form;
use Quiz\Models\Quiz;
use Quiz\Services\QuizServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Quiz\Unit
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class QuizServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /* Set up the service class*/
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(QuizServiceInterface::class);
    }

     /**
     * Browse
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_can_return_a_paginated_list_of_quiz()
    {
        // Arrangements
        $quiz = factory(Quiz::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

     /**
     * Browse
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_quiz()
    {
        // Arrangements
        $quiz = factory(Quiz::class, 10)->create();

        // Actions
        $actual = $this->service->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * Read
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_can_find_and_return_an_existing_quiz()
    {
        // Arrangements
        $expected = factory(Quiz::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Quiz::class, $actual);
    }

    /**
     * Read
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_will_abort_to_404_when_a_quiz_does_not_exist()
    {
        // Arrangements
        factory(Quiz::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * Edit
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_can_update_an_existing_quiz()
    {
         // Arrangements
        $quiz = factory(Quiz::class)->create();

        // Actions
        $attributes = [
            'title' => $title = $this->faker->unique()->words(10, true),
        ];
        $actual = $this->service->update($quiz->getKey(), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $this->assertTrue($actual);
    }

    /**
     * Edit
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_can_restore_a_soft_deleted_quiz()
    {
        // Arrangements
        $quiz = factory(Quiz::class)->create();
        $quiz->delete();

        // Actions
        $actual = $this->service->restore($quiz->getKey());
        $restored = $this->service->find($quiz->getKey());

        // Assertions
        $this->assertNull($actual);
        $this->assertNull($restored->deleted_at);
    }

    /**
     * Edit
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_can_restore_multiple_soft_deleted_quiz()
    {
        // Arrangements
        $quiz = factory(Quiz::class, 5)->create();
        $quiz->each->delete();

        // Actions
        $actual = $this->service->restore($quiz->pluck('id')->toArray());

        // Assertions
        $this->assertNull($actual);
        $quiz->each(function ($quiz) {
            $restored = $this->service->find($quiz->getKey());
            $this->assertNull($restored->deleted_at);
        });
    }

    /**
     * Add
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_can_store_a_quiz_to_database()
    {
        // Arrangements
        $attributes = factory(Quiz::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * Delete
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_can_soft_delete_an_existing_quiz()
    {
        // Arrangements
        $quiz = factory(Quiz::class, 3)->create()->random();

        // Actions
        $this->service->destroy($quiz->getKey());
        $quiz = $this->service->withTrashed()->find($quiz->getKey());

        // Assertions
        $this->assertSoftDeleted($this->service->getTable(), $quiz->toArray());
    }

    // **
    //  * Delete
    //  *
    //  * @test
    //  * @group  unit
    //  * @group  unit:quiz
    //  * @group  service
    //  * @group  service:quiz
    //  * @return void
    //  *
    // public function it_can_soft_delete_multiple_existing_quizzes()
    // {
    //     // Arrangements
    //     $quiz = factory(Quiz::class, 3)->create();

    //     // Actions
    //     $this->service->destroy($quiz->pluck('id')->toArray());

    //     // Assertions
    //     $quiz->each(function ($quiz) {
    //         $this->assertSoftDeleted($this->service->getTable(), $quiz->toArray());
    //     });
    // }

    /**
     * Delete
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_quiz()
    {
        // Arrangements
        $quiz = factory(Quiz::class)->create();
        $quiz->delete();

        // Actions
        $this->service->delete($quiz->getKey());

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $quiz->toArray());
    }

    /**
     * Delete
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_can_permanently_delete_multiple_soft_deleted_quizzes()
    {
        // Arrangements
        $quizzes = factory(Quiz::class, 5)->create();
        $quizzes->each->delete();

        // Actions
        $this->service->delete($quizzes->pluck('id')->toArray());

        // Assertions
        $quizzes->each(function ($quiz) {
            $this->assertDatabaseMissing($this->service->getTable(), $quiz->toArray());
        });
    }

    /**
     * Rules
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules($id = 1);

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertEquals('required|max:255', $rules['title']);
        $this->assertEquals('required|numeric', $rules['user_id']);
    }

    /**
     * Rules
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_should_return_an_array_of_messages()
    {
        // Arrangements
        $messages = $this->service->messages();

        // Assertions
        $this->assertIsArray($messages);
    }

    /**
     * Authorization
     *
     * @test
     * @group  unit
     * @group  unit:quiz
     * @group  service
     * @group  service:quiz
     * @return void
     */
    public function it_can_check_if_user_is_authorized_to_interact_with_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->withPermissionsPolicy();
        $restricted = factory(Quiz::class, 3)->create()->random();
        $quiz = factory(Quiz::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $restricted = $this->service->authorize($restricted);
        $authorized = $this->service->authorize($quiz);

        // Assertions
        $this->assertFalse($restricted);
        $this->assertTrue($authorized);
    }
}
