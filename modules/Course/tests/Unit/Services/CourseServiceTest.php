<?php

namespace Tests\Course\Unit\Services;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
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
class CourseServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CourseServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_return_a_paginated_list_of_courses()
    {
        // Arrangements
        $courses = factory(Course::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_courses()
    {
        // Arrangements
        $courses = factory(Course::class, 10)->create();
        $courses->each->delete();

        // Actions
        $actual = $this->service->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_find_and_return_an_existing_course()
    {
        // Arrangements
        $expected = factory(Course::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Course::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_will_abort_to_404_when_a_course_does_not_exist()
    {
        // Arrangements
        factory(Course::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_update_an_existing_course()
    {
        // Arrangements
        $course = factory(Course::class)->create();

        // Actions
        $attributes = [
            'title' => $title = $this->faker->unique()->words(10, true),
        ];
        $actual = $this->service->update($course->getKey(), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $this->assertTrue($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_restore_a_soft_deleted_course()
    {
        // Arrangements
        $course = factory(Course::class)->create();
        $course->delete();

        // Actions
        $actual = $this->service->restore($course->getKey());
        $restored = $this->service->find($course->getKey());

        // Assertions
        $this->assertNull($actual);
        $this->assertNull($restored->deleted_at);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_restore_multiple_soft_deleted_courses()
    {
        // Arrangements
        $courses = factory(Course::class, 5)->create();
        $courses->each->delete();

        // Actions
        $actual = $this->service->restore($courses->pluck('id')->toArray());

        // Assertions
        $this->assertNull($actual);
        $courses->each(function ($course) {
            $restored = $this->service->find($course->getKey());
            $this->assertNull($restored->deleted_at);
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_store_a_course_to_database()
    {
        // Arrangements
        $attributes = factory(Course::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_soft_delete_an_existing_course()
    {
        // Arrangements
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $this->service->destroy($course->getKey());
        $course = $this->service->withTrashed()->find($course->getKey());

        // Assertions
        $this->assertSoftDeleted($this->service->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_soft_delete_multiple_existing_courses()
    {
        // Arrangements
        $courses = factory(Course::class, 3)->create();

        // Actions
        $this->service->destroy($courses->pluck('id')->toArray());
        $courses = $this->service->withTrashed()->whereIn('id', $courses->pluck('id')->toArray())->get();

        // Assertions
        $courses->each(function ($course) {
            $this->assertSoftDeleted($this->service->getTable(), $course->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_course()
    {
        // Arrangements
        $course = factory(Course::class)->create();
        $course->delete();

        // Actions
        $this->service->delete($course->getKey());

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_permanently_delete_multiple_soft_deleted_courses()
    {
        // Arrangements
        $courses = factory(Course::class, 5)->create();
        $courses->each->delete();

        // Actions
        $this->service->delete($courses->pluck('id')->toArray());

        // Assertions
        $courses->each(function ($course) {
            $this->assertDatabaseMissing($this->service->getTable(), $course->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules($id = 1);

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('subtitle', $rules);
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertArrayHasKey('slug', $rules);
        $this->assertArrayHasKey('code', $rules);
        $this->assertEquals('required|max:255', $rules['title']);
        $this->assertEquals('required|max:255', $rules['subtitle']);
        $this->assertEquals('required|numeric', $rules['user_id']);
        $this->assertEquals([
            'required', 'regex:/[a-zA-Z0-9\s]+/', Rule::unique($this->service->getTable())->ignore($id)
        ], $rules['code']);
        $this->assertEquals([
            'required', 'alpha_dash', Rule::unique($this->service->getTable())->ignore($id)
        ], $rules['slug']);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
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
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_check_if_user_is_authorized_to_interact_with_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->withPermissionsPolicy();
        $restricted = factory(Course::class, 3)->create()->random();
        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $restricted = $this->service->authorize($restricted);
        $authorized = $this->service->authorize($course);

        // Assertions
        $this->assertFalse($restricted);
        $this->assertTrue($authorized);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_check_if_user_has_unrestricted_authorization_to_interact_with_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.unrestricted']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $unrestricted = $this->service->authorize($course);

        // Assertions
        $this->assertTrue($unrestricted);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_upload_a_given_file()
    {
        // Arrangements
        $fakeFile = UploadedFile::fake()->create('tmp.txt', 20);

        // Actions
        $actual = $this->service->upload($fakeFile);

        // Assertions
        $this->assertIsString($actual);
        $this->assertFileExists(storage_path(sprintf('%s/%s/%s',
            settings('storage:modules', 'modules/'.$this->service->getTable()),
            date('Y-m-d'), basename($actual)
        )));
    }
}
