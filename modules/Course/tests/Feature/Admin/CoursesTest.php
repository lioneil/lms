<?php

namespace Tests\Course\Feature\Admin;

use Core\Application\Permissions\PermissionsPolicy;
use Course\Models\Course;
use Course\Models\Lesson;
use Course\Models\Tag;
use Course\Services\CourseServiceInterface;
use Course\Services\LessonServiceInterface;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Taxonomy\Models\Category;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Course\Feature
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CoursesTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CourseServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.index
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $courses = factory(Course::class, 5)->create();

        // Actions
        $response = $this->get(route('courses.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('course::admin.index')
                 ->assertSeeText(trans('Add Course'))
                 ->assertSeeText(trans('All Courses'))
                 ->assertSeeTextInOrder($courses->pluck('title')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('subtitle')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('code')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('status')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.trashed
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_trashed_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $courses = factory(Course::class, 5)->create();
        $courses->each->delete();

        // Actions
        $response = $this->get(route('courses.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('course::admin.trashed')
                 ->assertSeeText(trans('Back to Courses'))
                 ->assertSeeText(trans('Archived Courses'))
                 ->assertSeeTextInOrder($courses->pluck('title')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('code')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Restore'), trans('Remove Permanently')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.show
     * @return void
     */
    public function a_super_user_can_visit_a_course_page()
    {
        // Arrangements
        $category = factory(Category::class)->create();
        $tags = factory(Tag::class, 2)->create();
        $course = factory(Course::class, 2)->create([
            'category_id' => $category->getKey(),
        ])->random();
        $course->tags()->sync($tags->pluck('id')->toArray());
        $lessons = factory(Lesson::class, 5)->create([
            'title' => sprintf('Lesson: %s', title_case($this->faker->words($count = 4, $asText = true))),
            'course_id' => $course->getKey(),
        ]);

        // Actions
        $this->actingAs($user = $this->superAdmin);
        $response = $this->get(route('courses.show', $course->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('course::admin.show')
                 ->assertSeeText($course->title)
                 ->assertSeeText($course->subtitle)
                 ->assertSeeText($course->status)
                 ->assertSeeText($course->code)
                 ->assertSeeText($course->author)
                 ->assertSeeText($course->description)
                 ->assertSeeText($course->category->name)
                 ->assertSeeTextInOrder($course->tags->pluck('name')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('title')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('slug')->toArray());
        $this->assertEquals($course->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.edit
     * @return void
     */
    public function a_super_user_can_visit_the_edit_course_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('courses.edit', $course->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewHas('resource')
                 ->assertViewIs('course::admin.edit')
                 ->assertSeeText(trans('Edit Course'))
                 ->assertSeeText($course->title)
                 ->assertSeeText($course->subtitle)
                 ->assertSeeText($course->code)
                 ->assertSeeText($course->description)
                 ->assertSeeText($course->icon)
                 ->assertSeeText($course->logo)
                 ->assertSeeText($course->user->getKey())
                 ->assertSeeText($course->category->getKey())
                 ->assertSeeTextInOrder($course->tags->pluck('id')->toArray())
                 ->assertSeeText(trans('Lessons'))
                 ->assertSeeText(trans('Update Course'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.update
     * @return void
     */
    public function a_super_user_can_update_a_course()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $attributes = factory(Course::class)->make()->toArray();
        $response = $this->put(route('courses.update', $course->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('courses.show', $course->getKey()));
        $this->assertDatabaseHas($course->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.restore
     * @return void
     */
    public function a_super_user_can_restore_a_course()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class, 3)->create()->random();
        $course->delete();

        // Actions
        $response = $this->patch(
            route('courses.restore', $course->getKey()), [],
            ['HTTP_REFERER' => route('courses.trashed')]
        );
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertRedirect(route('courses.trashed'));
        $this->assertFalse($course->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.restore
     * @return void
     */
    public function a_super_user_can_restore_multiple_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $courses = factory(Course::class, 3)->create();
        $courses->each->delete();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->patch(
            route('courses.restore'), $attributes, ['HTTP_REFERER' => route('courses.trashed')]
        );
        $courses = $this->service->whereIn('id', $courses->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('courses.trashed'));
        $courses->each(function ($course) {
            $this->assertFalse($course->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.create
     * @return void
     */
    public function a_super_user_can_visit_the_create_course_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);

        // Actions
        $response = $this->get(route('courses.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('course::admin.create')
                 ->assertSeeText(trans('Create Course'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('Subtitle'))
                 ->assertSeeText(trans('Code'))
                 ->assertSeeText(trans('Overview'))
                 ->assertSeeText(trans('Lessons'))
                 ->assertSeeText(trans('Save Course'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.store
     * @return void
     */
    public function a_super_user_can_store_a_course_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);

        // Actions
        $attributes = factory(Course::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('courses.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('courses.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_a_course()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->delete(
            route('courses.destroy', $course->getKey()), [],
            ['HTTP_REFERER' => route('courses.index')]
        );
        $course = $this->service->withTrashed()->find($course->getKey());

        // Assertions
        $response->assertRedirect(route('courses.index'));
        $this->assertSoftDeleted($course->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_multiple_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $courses = factory(Course::class, 2)->create();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->delete(
            route('courses.destroy', '@null'), $attributes, ['HTTP_REFERER' => route('courses.index')]
        );
        $courses = $this->service->withTrashed()->whereIn('id', $courses->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('courses.index'));
        $courses->each(function ($course) {
            $this->assertSoftDeleted($course->getTable(), $course->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_a_course()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class, 3)->create()->random();
        $course->delete();

        // Actions
        $response = $this->delete(
            route('courses.delete', $course->getKey()), [],
            ['HTTP_REFERER' => route('courses.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('courses.trashed'));
        $this->assertDatabaseMissing($course->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_multiple_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $courses = factory(Course::class, 3)->create();
        $courses->each->delete();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->delete(
            route('courses.delete'), $attributes, ['HTTP_REFERER' => route('courses.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('courses.trashed'));
        $courses->each(function ($course) {
            $this->assertDatabaseMissing($course->getTable(), $course->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.index
     * @group  user:courses.index
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.index', 'courses.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Course::class, 2)->create();
        $courses = factory(Course::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);

        // Actions
        $response = $this->get(route('courses.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('course::admin.index')
                 ->assertSeeText(trans('All Courses'))
                 ->assertSeeTextInOrder($courses->pluck('title')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('subtitle')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('code')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('status')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->title)
                 ->assertDontSeeText($restricted->random()->subtitle)
                 ->assertDontSeeText($restricted->random()->code)
                 ->assertDontSeeText($restricted->random()->author);
        $this->assertSame($courses->random()->author, $user->displayname);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.trashed
     * @group  user:courses.trashed
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_trashed_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.trashed', 'courses.owned']));
        $this->withPermissionsPolicy();

        $restricted = factory(Course::class, 2)->create();
        $restricted->each->delete();
        $courses = factory(Course::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);
        $courses->each->delete();

        // Actions
        $response = $this->get(route('courses.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('course::admin.trashed')
                 ->assertSeeText(trans('Back to Courses'))
                 ->assertSeeText(trans('Archived Courses'))
                 ->assertSeeTextInOrder($courses->pluck('title')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('subtitle')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('code')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('status')->toArray())
                 ->assertSeeTextInOrder($courses->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->title)
                 ->assertDontSeeText($restricted->random()->subtitle)
                 ->assertDontSeeText($restricted->random()->code)
                 ->assertDontSeeText($restricted->random()->author);
        $this->assertSame($courses->random()->author, $user->displayname);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.show
     * @group  user:courses.show
     * @return void
     */
    public function a_user_can_visit_owned_course_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'courses.edit', 'courses.show', 'courses.owned', 'courses.publish', 'courses.destroy',
            'courses.edit', 'courses.show', 'courses.owned',
            'courses.publish', 'courses.destroy'
        ]));
        $this->withPermissionsPolicy();

        $category = factory(Category::class)->create();
        $tags = factory(Tag::class, 3)->create();
        $course = factory(Course::class, 3)->create([
            'category_id' => $category->getKey(),
            'user_id' => $user->getKey(),
        ])->random();
        $course->tags()->sync($tags->pluck('id')->toArray());
        $lessons = factory(Lesson::class, 5)->create([
            'title' => sprintf('Lesson: %s', title_case($this->faker->words(4, $asText = true))),
            'course_id' => $course->getKey(),
        ]);

        // Actions
        $response = $this->get(route('courses.show', $course->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('course::admin.show')
                 ->assertSeeText($course->title)
                 ->assertSeeText($course->subtitle)
                 ->assertSeeText($course->code)
                 ->assertSeeText($course->author)
                 ->assertSeeText($course->description)
                 ->assertSeeText($course->category->name)
                 ->assertSeeTextInOrder($course->tags->pluck('name')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('title')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('slug')->toArray())
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash'), trans('Publish')]);
        $this->assertEquals($course->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.show
     * @group  user:courses.show
     * @return void
     */
    public function a_user_can_visit_any_course_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'courses.edit', 'courses.show', 'courses.owned', 'courses.publish', 'courses.destroy'
        ]));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'courses.edit', 'courses.show', 'courses.owned', 'courses.publish', 'courses.destroy'
        ]);
        $category = factory(Category::class)->create();
        $tags = factory(Tag::class, 3)->create();
        $course = factory(Course::class, 3)->create([
            'category_id' => $category->getKey(),
            'user_id' => $otherUser->getKey(),
        ])->random();
        $course->tags()->sync($tags->pluck('id')->toArray());
        $lessons = factory(Lesson::class, 10)->create([
            'title' => sprintf('Lesson: %s', ucwords($this->faker->words(4, $asText = true))),
            'course_id' => $course->getKey(),
        ]);

        // Actions
        $response = $this->get(route('courses.show', $course->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('course::admin.show')
                 ->assertSeeText($course->title)
                 ->assertSeeText($course->subtitle)
                 ->assertSeeText($course->code)
                 ->assertSeeText($course->author)
                 ->assertSeeText($course->description)
                 ->assertSeeText($course->category->name)
                 ->assertSeeTextInOrder($course->tags->pluck('name')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('title')->toArray())
                 ->assertSeeTextInOrder($course->lessons->pluck('slug')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.show
     * @group  user:courses.show
     * @return void
     */
    public function a_user_cannot_edit_others_courses_on_the_show_course_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'courses.edit', 'courses.show', 'courses.owned', 'courses.publish', 'courses.destroy'
        ]));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'courses.edit', 'courses.show', 'courses.owned', 'courses.publish', 'courses.destroy'
        ]);
        $category = factory(Category::class)->create();
        $tags = factory(Tag::class, 3)->create();
        $course = factory(Course::class, 3)->create([
            'category_id' => $category->getKey(),
            'user_id' => $otherUser->getKey(),
        ])->random();
        $course->tags()->sync($tags->pluck('id')->toArray());
        $lessons = factory(Lesson::class, 10)->create([
            'title' => sprintf('Lesson: %s', ucwords($this->faker->words(4, $asText = true))),
            'course_id' => $course->getKey(),
        ]);

        // Actions
        $response = $this->get(route('courses.show', $course->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('course::admin.show')
                 ->assertDontSeeText(trans('Edit'))
                 ->assertDontSeeText(trans('Move to Trash'))
                 ->assertDontSeeText(trans('Publish'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.edit
     * @group  user:courses.edit
     * @return void
     */
    public function a_user_can_only_visit_their_owned_edit_course_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.edit', 'courses.owned']));
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->get(route('courses.edit', $course->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.edit
     * @group  user:courses.edit
     * @return void
     */
    public function a_user_cannot_visit_others_edit_course_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.edit', 'courses.owned']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('courses.edit', $course->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.update
     * @group  user:courses.update
     * @return void
     */
    public function a_user_can_only_update_their_owned_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.owned', 'courses.update']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = factory(Course::class)->make()->toArray();
        $response = $this->put(route('courses.update', $course->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('courses.show', $course->getKey()));
        $this->assertDatabaseHas($course->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.update
     * @group  user:courses.update
     * @return void
     */
    public function a_user_cannot_update_others_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.owned', 'courses.update']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $attributes = factory(Course::class)->make()->toArray();
        $response = $this->put(route('courses.update', $course->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($course->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.restore
     * @group  user:courses.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.owned', 'courses.restore']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();
        $course->delete();

        // Actions
        $response = $this->patch(
            route('courses.restore', $course->getKey()), [],
            ['HTTP_REFERER' => route('courses.trashed')]
        );
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertRedirect(route('courses.trashed'));
        $this->assertFalse($course->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.restore
     * @group  user:courses.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_multiple_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.owned', 'courses.restore']));
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 3)->create(['user_id' => $user->getKey()]);
        $courses->each->delete();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->patch(
            route('courses.restore'), $attributes, ['HTTP_REFERER' => route('courses.trashed')]
        );
        $courses = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertRedirect(route('courses.trashed'));
        $courses->each(function ($course) {
            $this->assertFalse($course->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.restore
     * @group  user:courses.restore
     * @return void
     */
    public function a_user_cannot_restore_others_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.owned', 'courses.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['courses.owned', 'courses.restore']);
        $course = factory(Course::class, 3)->create(['user_id' => $otherUser->getKey()])->random();
        $course->delete();

        // Actions
        $response = $this->patch(
            route('courses.restore', $course->getKey()), [],
            ['HTTP_REFERER' => route('courses.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($course->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.restore
     * @group  user:courses.restore
     * @return void
     */
    public function a_user_cannot_restore_others_multiple_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.owned', 'courses.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['courses.owned', 'courses.restore']);
        $courses = factory(Course::class, 3)->create(['user_id' => $otherUser->getKey()]);
        $courses->each->delete();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->patch(
            route('courses.restore'), $attributes, ['HTTP_REFERER' => route('courses.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $courses->each(function ($course) {
            $this->assertDatabaseHas($course->getTable(), $course->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.create
     * @group  user:courses.create
     * @return void
     */
    public function a_user_can_visit_the_create_course_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.create']));
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->get(route('courses.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('course::admin.create')
                 ->assertSeeText(trans('Create Course'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('Subtitle'))
                 ->assertSeeText(trans('Code'))
                 ->assertSeeText(trans('Overview'))
                 ->assertSeeText(trans('Lessons'))
                 ->assertSeeText(trans('Save Course'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.store
     * @group  user:courses.store
     * @return void
     */
    public function a_user_can_store_a_course_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.create', 'courses.store']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Course::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('courses.store'), $attributes);

        // Assertions
        $response->assertRedirect(route('courses.index'));
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.destroy
     * @group  user:courses.destroy
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'courses.index', 'courses.destroy', 'courses.owned'
        ]));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(
            route('courses.destroy', $course->getKey()), [],
            ['HTTP_REFERER' => route('courses.index')]
        );
        $course = $this->service->withTrashed()->find($course->getKey());

        // Assertions
        $response->assertRedirect(route('courses.index'));
        $this->assertSoftDeleted($course->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.destroy
     * @group  user:courses.destroy
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.index', 'courses.destroy', 'courses.owned']));
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 2)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->delete(
            route('courses.destroy', '@null'), $attributes, ['HTTP_REFERER' => route('courses.index')]
        );
        $courses = $this->service->withTrashed()->whereIn('id', $courses->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('courses.index'));
        $courses->each(function ($course) {
            $this->assertSoftDeleted($course->getTable(), $course->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.destroy
     * @group  user:courses.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.trashed', 'courses.destroy', 'courses.owned']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();
        $course->delete();

        // Actions
        $response = $this->delete(
            route('courses.destroy', $course->getKey()), [],
            ['HTTP_REFERER' => route('courses.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($course->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.destroy
     * @group  user:courses.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_multiple_others_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.trashed', 'courses.destroy', 'courses.owned']));
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 3)->create();
        $courses->each->delete();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->delete(
            route('courses.destroy', '@null'), $attributes, ['HTTP_REFERER' => route('courses.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $courses->each(function ($course) {
            $this->assertDatabaseHas($course->getTable(), $course->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.delete
     * @group  user:courses.delete
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.trashed', 'courses.delete', 'courses.owned']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();
        $course->delete();

        // Actions
        $response = $this->delete(
            route('courses.delete', $course->getKey()), [],
            ['HTTP_REFERER' => route('courses.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('courses.trashed'));
        $this->assertDatabaseMissing($course->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.delete
     * @group  user:courses.delete
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.trashed', 'courses.delete', 'courses.owned']));
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 3)->create(['user_id' => $user->getKey()]);
        $courses->each->delete();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->delete(
            route('courses.delete'), $attributes, ['HTTP_REFERER' => route('courses.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('courses.trashed'));
        $courses->each(function ($course) {
            $this->assertDatabaseMissing($course->getTable(), $course->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.delete
     * @group  user:courses.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.trashed', 'courses.delete', 'courses.owned']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();
        $course->delete();

        // Actions
        $response = $this->delete(
            route('courses.delete', $course->getKey()), [],
            ['HTTP_REFERER' => route('courses.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($course->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.delete
     * @group  user:courses.delete
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_others_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.trashed', 'courses.delete', 'courses.owned']));
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 3)->create();
        $courses->each->delete();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->delete(
            route('courses.delete'), $attributes, ['HTTP_REFERER' => route('courses.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $courses->each(function ($course) {
            $this->assertDatabaseHas($course->getTable(), $course->toArray());
        });
    }
}
