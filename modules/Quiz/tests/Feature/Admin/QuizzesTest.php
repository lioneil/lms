<?php

namespace Tests\Quiz\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Quiz\Models\Form;
use Quiz\Services\QuizServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Quiz\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class QuizzesTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker ,ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(QuizServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.index
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_quizzes()
    {
        // Arrangements
        // $this->withoutExceptionHandling();
        $this->actingAs($user = $this->superAdmin);
        $quizzes = factory(Form::class, 5)->create();

        // Actions
        $response = $this->get(route('quizzes.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('quiz::admin.index')
                 ->assertSeeText(trans('Add Quiz'))
                 ->assertSeeText(trans('All Quizzes'))
                 ->assertSeeTextInOrder($quizzes->pluck('title')->toArray())
                 ->assertSeeTextInOrder($quizzes->pluck('description')->toArray())
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
    }

    /**
     * Browse test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.trashed
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_trashed_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $quizzes = factory(Form::class, 5)->create();
        $quizzes->each->delete();
        // Actions
        $response = $this->get(route('quizzes.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('quiz::admin.trashed')
                 ->assertSeeText(trans('Back to Quizzes'))
                 ->assertSeeText(trans('Archived Quizzes'))
                 ->assertSeeTextInOrder($quizzes->pluck('title')->toArray())
                 ->assertSeeTextInOrder($quizzes->pluck('description')->toArray())
                 ->assertSeeTextInOrder([trans('Restore'), trans('Remove Permanently')]);

    }

    /**
     * Read Test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.show
     * @return void
     */
    public function a_super_user_can_visit_a_quiz_page()
    {
        // Arrangements
        // $this->withoutExceptionHandling();
        $this->actingAs($user = $this->asSuperAdmin());
        $quizzes = factory(Form::class, 4)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('quizzes.show', $quizzes->getKey()));

        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('quiz::admin.show')
                 ->assertSeeText($quizzes->title)
                 ->assertSeeText($quizzes->description);
        $this->assertEquals($quizzes->getKey(), $actual->getKey());
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.edit
     * @return void
     */
    public function a_super_user_can_visit_the_edit_quiz_page()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        $this->actingAs($user = $this->asSuperAdmin());
        $quiz = factory(Form::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('quizzes.edit', $quiz->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewHas('resource')
                 ->assertViewIs('quiz::admin.edit')
                 ->assertSeeText(trans('Edit Quiz'))
                 ->assertSeeText($quiz->title)
                 ->assertSeeText($quiz->url)
                 ->assertSeeText($quiz->description)
                 ->assertSeeText(trans('Update Quiz'));
    }

     /**
     * Test Pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.update
     * @return void
     */
    public function a_super_user_can_update_a_quiz()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        $this->actingAs($user = $this->asSuperAdmin());
        $quiz = factory(Form::class, 3)->create()->random();

        // Actions
        $attributes = ['title' => $this->faker->words($count = 5, $asText = true)];
        $response = $this->put(route('quizzes.update', $quiz->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('quizzes.show', $quiz->getKey()));
        $this->assertDatabaseHas($quiz->getTable(), $attributes);
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.restore
     * @return void
     */
    public function a_super_user_can_restore_a_quiz()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $quiz = factory(Form::class, 3)->create()->random();
        $quiz->delete();

        // Actions
        $response = $this->patch(
            route('quizzes.restore', $quiz->getKey()), [], ['HTTP_REFERER' => route('quizzes.trashed')]
        );
        $quiz = $this->service->find($quiz->getKey());

        // Assertions
        $response->assertRedirect(route('quizzes.trashed'));
        $this->assertFalse($quiz->trashed());
    }

     /**
     * Edit // test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.restore
     * @return void
     */
    public function a_super_user_can_restore_multiple_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $quizzes = factory(Form::class, 3)->create();
        $quizzes->each->delete();

        // Actions
        $attributes = ['id' => $quizzes->pluck('id')->toArray()];
        $response = $this->patch(
            route('quizzes.restore'), $attributes, ['HTTP_REFERER' => route('quizzes.trashed')]
        );
        $quizzes = $this->service->whereIn('id', $quizzes->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('quizzes.trashed'));
        $quizzes->each(function ($quiz) {
            $this->assertFalse($quiz->trashed());
        });
    }

     /**
     * Add. test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.create
     * @return void
     */
    public function a_super_user_can_visit_the_create_quiz_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());

        // Actions
        $response = $this->get(route('quizzes.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('quiz::admin.create')
                 ->assertSeeText(trans('Create Quiz'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('Description'))
                 ->assertSeeText(trans('Save Quiz'));
    }

    /**
     * Add. Failed asserting that a row in the table [coursewares] matches the attributes {
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.store
     * @return void
     */
    public function a_super_user_can_store_a_quiz_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());

        // Actions
        $attributes = factory(Form::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('quizzes.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('quizzes.index'));
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_a_quiz()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $quiz = factory(Form::class, 3)->create()->random();

        // Actions
        $response = $this->delete(
            route('quizzes.destroy', $quiz->getKey()), [], ['HTTP_REFERER' => route('quizzes.index')]
        );
        $quiz = $this->service->withTrashed()->find($quiz->getKey());

        // Assertions
        $response->assertRedirect(route('quizzes.index'));
        $this->assertSoftDeleted($quiz->getTable(), $quiz->toArray());
    }

    /**
     * Delete. Test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_multiple_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $quizzes = factory(Form::class, 3)->create();

        // Actions
        $attributes = ['id' => $quizzes->pluck('id')->toArray()];
        $response = $this->delete(route('quizzes.destroy', $single = 'false'), $attributes);
        $quizzes = $this->service->withTrashed()->whereIn('id', $quizzes->pluck('id')->toArray())->get();
        $response->assertRedirect(route('quizzes.index'));
        $quizzes->each(function ($quizzes) {
            $this->assertSoftDeleted($quizzes->getTable(), $quizzes->toArray());
        });
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_a_quiz()
    {
         // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $quiz = factory(Form::class, 3)->create()->random();
        $quiz->delete();

        // Actions
        $response = $this->delete(
            route('quizzes.delete', $quiz->getKey()), [], ['HTTP_REFERER' => route('quizzes.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('quizzes.trashed'));
        $this->assertDatabaseMissing($quiz->getTable(), $quiz->toArray());
    }

    /**
     * Test Pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_multiple_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $quizzes = factory(Form::class, 3)->create();
        $quizzes->each->delete();

        // Actions
        $attributes = ['id' => $quizzes->pluck('id')->toArray()];
        $response = $this->delete(
            route('quizzes.delete'), $attributes, ['HTTP_REFERER' => route('quizzes.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('quizzes.trashed'));
        $quizzes->each(function ($quiz) {
            $this->assertDatabaseMissing($quiz->getTable(), $quiz->toArray());
        });
    }

    /**
     * Browse test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.index
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.index', 'quizzes.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Form::class, 2)->create();
        $quizzes = factory(Form::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);

        // Actions
        $response = $this->get(route('quizzes.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('quiz::admin.index')
                 ->assertSeeText(trans('All Quizzes'))
                 ->assertSeeTextInOrder($quizzes->pluck('title')->toArray())
                 ->assertSeeTextInOrder($quizzes->pluck('description')->toArray())
                 ->assertDontSeeText($restricted->random()->title)
                 ->assertDontSeeText($restricted->random()->description);
        $this->assertSame(e($quizzes->random()->author), e($user->displayname));
    }

    /**
     * Test Pass
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.trashed
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_trashed_quizzes()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.trashed', 'quizzes.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Form::class, 2)->create();
        $quizzes = factory(Form::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);
        $quizzes->each->delete();

        // Actions
        $response = $this->get(route('quizzes.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('quiz::admin.trashed')
                 ->assertSeeText(trans('All Quizzes'))
                 ->assertSeeText(trans('Archived Quizzes'))
                 ->assertSeeTextInOrder($quizzes->pluck('title')->toArray())
                 ->assertSeeTextInOrder($quizzes->pluck('description')->toArray())
                 ->assertDontSeeText($restricted->random()->title)
                 ->assertDontSeeText($restricted->random()->description);
        $this->assertSame($quizzes->random()->author, $user->displayname);
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.show
     * @return void
     */
    public function a_user_can_visit_owned_quiz_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'quizzes.edit', 'quizzes.show', 'quizzes.owned', 'quizzes.destory'
        ]));
        $this->withPermissionsPolicy();

        $quiz = factory(Form::class, 3)->create([
            'user_id' => $user->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('quizzes.show', $quiz->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('quiz::admin.show')
                 ->assertSeeText($quiz->title)
                 ->assertSeeText($quiz->description);
        $this->assertEquals($quiz->getKey(), $actual->getKey());
    }

     /**
     * Test pass
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.show
     * @group  user:quizzes.show
     * @return void
     */
    public function a_user_can_visit_any_quiz_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'quizzes.edit', 'quizzes.show', 'quizzes.owned', 'quizzes.destroy'
        ]));
        $this->actingAs($otherUser = $this->asNonSuperAdmin([
            'quizzes.edit', 'quizzes.show', 'quizzes.owned', 'quizzes.destroy'
        ]));

        $this->withPermissionsPolicy();
        $quiz = factory(Form::class, 4)->create([
            'user_id' => $user->getKey(),
        ])->random();

        $quiz = factory(Form::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('quizzes.show', $quiz->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('quiz::admin.show')
                 ->assertSeeText($quiz->title)
                 ->assertSeeText($quiz->description);
        $this->assertEquals($quiz->getKey(), $actual->getKey());
    }

    /**
     * test pass
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  courses.show
     * @group  user:quizzes.show
     * @return void
     */
    public function a_user_cannot_edit_others_materials_on_the_show_quiz_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'quizzes.edit', 'quizzes.show', 'quizzes.owned', 'quizzes.destroy'
        ]));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'quizzes.edit', 'quizzes.show', 'quizzes.owned', 'quizzes.destroy'
        ]);

        $quiz = factory(Form::class, 3)->create([
            'user_id' => $otherUser->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('quizzes.show', $quiz->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('quiz::admin.show')
                 ->assertDontSeeText(trans('Edit'))
                 ->assertDontSeeText(trans('Move to Trash'));
    }

    /**
     * test pass
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  quizzes.edit
     * @group  user:quizzes.edit
     * @return void
     */
    public function a_user_can_only_visit_their_owned_edit_quiz_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.edit', 'quizzes.update']));
        $this->withPermissionsPolicy();

        $quiz = factory(Form::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->get(route('quizzes.edit', $quiz->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  userquizzes.edit
     * @return void
     */
    public function a_user_cannot_visit_others_edit_quiz_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.edit', 'quizzes.update', 'quizzes.owned']));
        $quiz = factory(Form::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('quizzes.edit', $quiz->getKey()));

        // Assertions
        $response->assertForbidden();
    }

     /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.update
     * @return void
     */
    public function a_user_can_only_update_their_owned_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.owned', 'quizzes.update']));
        $this->withPermissionsPolicy();
        $quiz = factory(Form::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['title' => $this->faker->words(5, $asText = true)];
        $response = $this->put(route('quizzes.update', $quiz->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('quizzes.show', $quiz->getKey()));
        $this->assertDatabaseHas($quiz->getTable(), $attributes);
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.update
     * @return void
     */
    public function a_user_cannot_update_others_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.owned', 'quizzes.update']));
        $this->withPermissionsPolicy();
        $quiz = factory(Form::class, 3)->create()->random();

        // Actions
        $attributes = ['title' => $this->faker->words(5, $asText = true)];
        $response = $this->put(route('quizzes.update', $quiz->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($quiz->getTable(), $attributes);
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.owned', 'quizzes.restore']));
        $this->withPermissionsPolicy();
        $quiz = factory(Form::class, 3)->create(['user_id' => $user->getKey()])->random();
        $quiz->delete();

        // Actions
        $response = $this->patch(
            route('quizzes.restore', $quiz->getKey()), [], ['HTTP_REFERER' => route('quizzes.trashed')]
        );
        $quiz = $this->service->find($quiz->getKey());

        // Assertions
        $response->assertRedirect(route('quizzes.trashed'));
        $this->assertFalse($quiz->trashed());
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_multiple_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.owned', 'quizzes.restore']));
        $this->withPermissionsPolicy();
        $quizzes = factory(Form::class, 3)->create(['user_id' => $user->getKey()]);
        $quizzes->each->delete();

        // Actions
        $attributes = ['id' => $quizzes->pluck('id')->toArray()];
        $response = $this->patch(
            route('quizzes.restore'), $attributes, ['HTTP_REFERER' => route('quizzes.trashed')]
        );
        $quizzes = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertRedirect(route('quizzes.trashed'));
        $quizzes->each(function ($quiz) {
            $this->assertFalse($quiz->trashed());
        });
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.restore
     * @return void
     */
    public function a_user_cannot_restore_others_quiz()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.owned', 'quizzes.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['quizzes.owned', 'quizzes.restore']);
        $quiz = factory(Form::class, 3)->create(['user_id' => $otherUser->getKey()])->random();
        $quiz->delete();

        // Actions
        $response = $this->patch(
            route('quizzes.restore', $quiz->getKey()), [], ['HTTP_REFERER' => route('quizzes.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($quiz->getTable(), $quiz->toArray());
    }

    /**
     * Edit test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.restore
     * @return void
     */
    public function a_user_cannot_restore_others_multiple_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.owned', 'quizzes.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['quizzes.owned', 'quizzes.restore']);
        $quizzes = factory(Form::class, 3)->create(['user_id' => $otherUser->getKey()]);
        $quizzes->each->delete();

        // Actions
        $attributes = ['id' => $quizzes->pluck('id')->toArray()];
        $response = $this->patch(
            route('quizzes.restore'), $attributes, ['HTTP_REFERER' => route('quizzes.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $quizzes->each(function ($quiz) {
            $this->assertDatabaseHas($quiz->getTable(), $quiz->toArray());
        });
    }

    /**
     * Add test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.create
     * @return void
     */
    public function a_user_can_visit_the_create_quiz_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.create']));
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->get(route('quizzes.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('quiz::admin.create')
                 ->assertSeeText(trans('Create Quiz'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('Description'))
                 ->assertSeeText(trans('Save Quiz'));
    }

    /**
     * Add test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.store
     * @return void
     */
    public function a_user_can_store_a_quiz_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.create', 'quizzes.store']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Form::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('quizzes.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('quizzes.index'));
    }

     /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.destroy
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_quiz()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.destroy', 'quizzes.owned']));
        $this->withPermissionsPolicy();
        $quiz = factory(Form::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('quizzes.destroy', $quiz->getKey()));

        // Assertions
        $response->assertRedirect(route('quizzes.index'));
        $this->assertSoftDeleted($quiz->getTable(), $quiz->toArray());
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.destroy
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.destroy', 'quizzes.owned']));
        $this->withPermissionsPolicy();
        $quizzes = factory(Form::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $quizzes->pluck('id')->toArray()];
        $response = $this->delete(route('quizzes.destroy', $single = 'false'), $attributes);

        // Assertions
        $response->assertRedirect(route('quizzes.index'));
        $quizzes->each(function ($quiz) {
            $this->assertSoftDeleted($quiz->getTable(), $quiz->toArray());
        });
    }

    /**
     * Delete test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_quiz()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.destroy', 'quizzes.owned']));
        $this->withPermissionsPolicy();
        $quiz = factory(Form::class, 3)->create()->random();
        $quiz->delete();

        // Actions
        $response = $this->delete(route('quizzes.destroy', $quiz->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($quiz->getTable(), $quiz->toArray());
    }

    /**
     * Delete test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_multiple_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.destroy', 'quizzes.owned']));
        $this->withPermissionsPolicy();
        $quizzes = factory(Form::class, 3)->create();
        $quizzes->each->delete();

        // Actions
        $attributes = ['id' => $quizzes->pluck('id')->toArray()];
        $response = $this->delete(
            route('quizzes.destroy', $single = 'false'), $attributes);

        // Assertions
        $response->assertForbidden();
        $quizzes->each(function ($quiz) {
            $this->assertDatabaseHas($quiz->getTable(), $quiz->toArray());
        });
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.delete
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_quiz()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.trashed', 'quizzes.delete', 'quizzes.owned']));
        $this->withPermissionsPolicy();
        $quiz = factory(Form::class, 3)->create(['user_id' => $user->getKey()])->random();
        $quiz->delete();

        // Actions
        $response = $this->delete(
            route('quizzes.delete', $quiz->getKey()), [], ['HTTP_REFERER' => route('quizzes.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('quizzes.trashed'));
        $this->assertDatabaseMissing($quiz->getTable(), $quiz->toArray());
    }

    /**
     * Test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.delete
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.trashed', 'quizzes.delete', 'quizzes.owned']));
        $this->withPermissionsPolicy();
        $quizzes = factory(Form::class, 3)->create(['user_id' => $user->getKey()]);
        $quizzes->each->delete();

        // Actions
        $attributes = ['id' => $quizzes->pluck('id')->toArray()];
        $response = $this->delete(
            route('quizzes.delete'), $attributes, ['HTTP_REFERER' => route('quizzes.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('quizzes.trashed'));
        $quizzes->each(function ($quiz) {
            $this->assertDatabaseMissing($quiz->getTable(), $quiz->toArray());
        });
    }

    /**
     * test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_quiz()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.trashed', 'quizzes.delete', 'quizzes.owned']));
        $this->withPermissionsPolicy();
        $quiz = factory(Form::class, 3)->create()->random();
        $quiz->delete();

        // Actions
        $response = $this->delete(
            route('quizzes.delete', $quiz->getKey()), [], ['HTTP_REFERER' => route('quizzes.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($quiz->getTable(), $quiz->toArray());
    }

    /**
     * Delete test pass
     *
     * @test
     * @group  feature
     * @group  feature:quiz
     * @group  user:quizzes.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_multiple_quizzes()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['quizzes.trashed', 'quizzes.delete', 'quizzes.owned']));
        $this->withPermissionsPolicy();
        $quizzes = factory(Form::class, 3)->create();
        $quizzes->each->delete();

        // Actions
        $attributes = ['id' => $quizzes->pluck('id')->toArray()];
        $response = $this->delete(
            route('quizzes.delete'), $attributes, ['HTTP_REFERER' => route('quizzes.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $quizzes->each(function ($quiz) {
            $this->assertDatabaseHas($quiz->getTable(), $quiz->toArray());
        });
    }
}
