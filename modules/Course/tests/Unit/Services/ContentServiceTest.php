<?php

namespace Course\Unit\Services;

use Core\Rules\MimeIf;
use Course\Models\Content;
use Course\Services\ContentServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Unit\Services
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ContentServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ContentServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_can_return_a_paginated_list_of_contents()
    {
        // Arrangements
        $contents = factory(Content::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_contents()
    {
        // Arrangements
        $contents = factory(Content::class, 10)->create();
        $contents->each->delete();

        // Actions
        $actual = $this->service->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_can_find_and_return_an_existing_content()
    {
        // Arrangements
        $expected = factory(Content::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Content::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_will_abort_to_404_when_a_content_does_not_exist()
    {
        // Arrangements
        factory(Content::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_can_update_an_existing_content()
    {
        // Arrangements
        $content = factory(Content::class)->create();

        // Actions
        $attributes = [
            'title' => $title = $this->faker->unique()->words(10, true),
        ];
        $actual = $this->service->update($content->getKey(), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $this->assertTrue($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_can_restore_a_soft_deleted_content()
    {
        // Arrangements
        $content = factory(Content::class)->create();
        $content->delete();

        // Actions
        $actual = $this->service->restore($content->getKey());
        $restored = $this->service->find($content->getKey());

        // Assertions
        $this->assertNull($actual);
        $this->assertNull($restored->deleted_at);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_can_restore_multiple_soft_deleted_contents()
    {
        // Arrangements
        $contents = factory(Content::class, 5)->create();
        $contents->each->delete();

        // Actions
        $actual = $this->service->restore($contents->pluck('id')->toArray());

        // Assertions
        $this->assertNull($actual);
        $contents->each(function ($content) {
            $restored = $this->service->find($content->getKey());
            $this->assertNull($restored->deleted_at);
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_can_store_a_content_to_database()
    {
        // Arrangements
        $attributes = factory(Content::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_can_soft_delete_an_existing_content()
    {
        // Arrangements
        $content = factory(Content::class, 3)->create()->random();

        // Actions
        $this->service->destroy($content->getKey());
        $content = $this->service->withTrashed()->find($content->getKey());

        // Assertions
        $this->assertSoftDeleted($this->service->getTable(), $content->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_can_soft_delete_multiple_existing_contents()
    {
        // Arrangements
        $contents = factory(Content::class, 3)->create();

        // Actions
        $this->service->destroy($contents->pluck('id')->toArray());
        $contents = $this->service->withTrashed()->whereIn('id', $contents->pluck('id')->toArray())->get();

        // Assertions
        $contents->each(function ($content) {
            $this->assertSoftDeleted($this->service->getTable(), $content->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_content()
    {
        // Arrangements
        $content = factory(Content::class)->create();
        $content->delete();

        // Actions
        $this->service->delete($content->getKey());

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $content->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_can_permanently_delete_multiple_soft_deleted_contents()
    {
        // Arrangements
        $contents = factory(Content::class, 5)->create();
        $contents->each->delete();

        // Actions
        $this->service->delete($contents->pluck('id')->toArray());

        // Assertions
        $contents->each(function ($content) {
            $this->assertDatabaseMissing($this->service->getTable(), $content->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
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
        $this->assertArrayHasKey('course_id', $rules);
        $this->assertArrayHasKey('slug', $rules);
        $this->assertArrayHasKey('content', $rules);
        $this->assertEquals('required|max:255', $rules['title']);
        $this->assertEquals('required|max:255', $rules['subtitle']);
        $this->assertEquals('required|numeric', $rules['course_id']);
        $this->assertEquals(['required', new MimeIf], $rules['content']);
        $this->assertEquals([
            'sometimes',
            'required',
            Rule::unique($this->service->getTable())->ignore($id)->where(function ($query) {
                return $query->where('course_id', 1);
            }),
        ], $rules['slug']);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:content
     * @group  service
     * @group  service:content
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
     * @group  unit:content
     * @group  service
     * @group  service:content
     * @return void
     */
    public function it_can_upload_a_given_file()
    {
        // Arrangements
        $fakeFile = UploadedFile::fake()->create('tmp.pdf', 20);

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
