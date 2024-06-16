<?php

namespace Course\Feature\Api;

use Course\Models\Content;
use Course\Models\Course;
use Course\Services\ContentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CourseContentsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ContentServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:content
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_contents()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.index', 'contents.owned']), ['contents.index']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $contents = factory(Content::class, 2)->create(['course_id' => $course->getKey()]);

        // Actions
        $response = $this->get(route('api.contents.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [[
                    'course_id' => $course->getKey(),
                ]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'title', 'subtitle', 'slug',
                        'description', 'content', 'sort',
                        'type', 'course_id', 'author',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:content
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_trashed_contents()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.trashed', 'contents.owned']), ['contents.trashed']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $contents = factory(Content::class, 5)->create(['course_id' => $course->getKey()]);
        $contents->each->delete();

        // Actions
        $response = $this->get(route('api.contents.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [[
                    'course_id' => $course->getKey(),
                 ]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'course_id',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:content
     * @return void
     */
    public function a_user_can_visit_owned_content_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.show', 'contents.owned']), ['contents.show']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $content = factory(Content::class, 5)->create(['course_id' => $course->getKey()])->random();

        // Actions
        $response = $this->get(route('api.contents.show', $course->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'course_id' => $course->getKey(),
                 ]])
                 ->assertJsonStructure([
                    'data' => [
                        'course_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:content
     * @return void
     */
    public function a_user_can_visit_any_content_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.show']), ['contents.show']);
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'contents.edit', 'contents.show', 'contents.owned', 'contents.publish', 'contents.destroy'
        ]);

        $course = factory(Course::class)->create();
        $content = factory(Content::class, 5)->create(['course_id' => $course->getKey()])->random();

        // Actions
        $response = $this->get(route('api.contents.show', $course->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'course_id' => $course->getKey(),
                 ]])
                 ->assertJsonStructure([
                    'data' => [
                        'course_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:content
     * @return void
     */
    public function a_user_can_store_a_content_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.store']), ['contents.store']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();

        // Actions
        $attributes = factory(Content::class)->make([
            'content' => UploadedFile::fake()->create('test.pdf'),
            'course_id' => $course->getKey()
        ])->toArray();

        $attributes['filetype'] = 'pdf';
        $response = $this->post(route('api.contents.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:content
     * @return void
     */
    public function a_user_can_only_update_their_owned_contents()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.owned', 'contents.update']), ['contents.update']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $content = factory(Content::class, 3)->create(['course_id' => $course->getKey()])->random();

        // Actions
        $attributes = factory(Content::class)->make()->toArray();
        $response = $this->put(route('api.contents.update', $content->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($content->getTable(), collect($attributes)->except('slug')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:content
     * @return void
     */
    public function a_user_can_only_restore_owned_content()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.restore', 'contents.owned']), ['contents.restore']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $content = factory(Content::class, 3)->create(['course_id' => $course->getKey()])->random();

        // Actions
        $response = $this->patch(route('api.contents.restore', $content->getKey()));
        $content = $this->service->find($content->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($content->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:content
     * @return void
     */
    public function a_user_can_only_restore_owned_multiple_contents()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.restore', 'contents.owned']), ['contents.restore']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $contents = factory(Content::class, 3)->create(['course_id' => $course->getKey()]);

        // Actions
        $attributes = ['id' => $contents->pluck('id')->toArray()];
        $response = $this->patch(route('api.contents.restore'), $attributes);
        $contents = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $contents->each(function ($content) {
            $this->assertFalse($content->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:content
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_content()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.destroy', 'contents.owned']), ['contents.destroy']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $content = factory(Content::class, 3)->create(['course_id' => $course->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.contents.destroy', $content->getKey()));
        $content = $this->service->withTrashed()->find($content->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($content->getTable(), $content->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:content
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_contents()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.destroy', 'contents.owned']), ['contents.destroy']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $contents = factory(Content::class, 2)->create(['course_id' => $course->getKey()]);

        // Actions
        $attributes = ['id' => $contents->pluck('id')->toArray()];
        $response = $this->delete(route('api.contents.destroy', 'null'), $attributes);
        $contents = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $contents->each(function ($content) {
            $this->assertSoftDeleted($content->getTable(), $content->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:content
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_content()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.delete', 'contents.owned']), ['contents.delete']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $content = factory(Content::class, 3)->create(['course_id' => $course->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.contents.delete', $content->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($content->getTable(), $content->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:content
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_contents()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['contents.delete', 'contents.owned']), ['contents.delete']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        $contents = factory(Content::class, 3)->create(['course_id' => $course->getKey()])->random();

        // Actions
        $attributes = ['id' => $contents->pluck('id')->toArray()];
        $response = $this->delete(route('api.contents.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $contents->each(function ($content) {
            $this->assertDatabaseMissing($content->getTable(), $content->toArray());
        });
    }
}
