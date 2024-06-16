<?php

namespace Page\Unit;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Page\Models\Page;
use Page\Services\PageServiceInterface;
use Taxonomy\Models\Category;
use Template\Models\Template;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Page\Unit
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PageServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(PageServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_return_a_paginated_list_of_pages()
    {
        // Arrangements
        $pages = factory(Page::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trahed_pages()
    {
        // Arrangements
        $pages = factory(Page::class, 10)->create();
        $pages->each->delete();

        // Actions
        $actual = $this->service->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_find_and_return_an_existing_page()
    {
        // Arrangements
        $expected = factory(Page::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Page::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_will_abort_to_404_when_a_page_does_not_exist()
    {
        // Arrangements
        factory(Page::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_update_an_existing_page()
    {
       // Arrangements
       $page = factory(Page::class)->create();

       // Actions
       $attributes = [
            'title' => $title = $this->faker->unique()->words(10, true),
            'code' => Str::slug($title),
            'feature' => $this->faker->text(),
            'template_id' => factory(Template::class)->create()->getKey(),
            'user_id' => factory(User::class)->create()->getKey(),
        ];
       $actual = $this->service->update($page->getKey(), $attributes);

       // Assertions
       $this->assertDatabaseHas($this->service->getTable(), $attributes);
       $this->assertTrue($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_restore_a_soft_deleted_page()
    {
        // Arrangements
        $page = factory(Page::class)->create();
        $page->delete();

        // Actions
        $actual = $this->service->restore($page->getKey());
        $restored = $this->service->find($page->getKey());

        // Assertions
        $this->assertNull($actual);
        $this->assertNull($restored->deleted_at);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_restore_multiple_soft_deleted_page()
    {
        // Arrangements
        $pages = factory(Page::class, 5)->create();
        $pages->each->delete();

        // Actions
        $actual = $this->service->restore($pages->pluck('id')->toArray());

        // Assertions
        $this->assertNull($actual);
        $pages->each(function ($page) {
            $restored = $this->service->find($page->getKey());
            $this->assertNull($restored->deleted_at);
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_store_a_page_to_database()
    {
        // Arrangements
        $attributes = factory(Page::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_soft_delete_an_existing_page()
    {
        // Arrangements
        $page = factory(Page::class, 3)->create()->random();

        // Actions
        $this->service->destroy($page->getKey());
        $page = $this->service->withTrashed()->find($page->getKey());

        // Assertions
        $this->assertSoftDeleted($this->service->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_soft_delete_multiple_existing_pages()
    {
        // Arrangements
        $pages = factory(Page::class, 3)->create();

        // Actions
        $this->service->destroy($pages->pluck('id')->toArray());
        $pages = $this->service->withTrashed()->whereIn('id', $pages->pluck('id')->toArray())->get();

        // Assertions
        $pages->each(function ($page) {
            $this->assertSoftDeleted($this->service->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_page()
    {
        // Arrangements
        $page = factory(Page::class)->create();
        $page->delete();

        // Actions
        $this->service->delete($page->getKey());

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_permanently_delete_multiple_soft_deleted_pages()
    {
        // Arrangements
        $pages = factory(Page::class, 5)->create();
        $pages->each->delete();

        // Actions
        $this->service->delete($pages->pluck('id')->toArray());

        // Assertions
        $pages->each(function ($page) {
            $this->assertDatabaseMissing($this->service->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules($id = 1);

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('feature', $rules);
        $this->assertArrayHasKey('template_id', $rules);
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertArrayHasKey('code', $rules);
        $this->assertEquals('required|max:255', $rules['title']);
        $this->assertEquals('required|max:255', $rules['feature']);
        $this->assertEquals('required|numeric', $rules['template_id']);
        $this->assertEquals('required|numeric', $rules['user_id']);
        $this->assertEquals([
            'required', 'alpha_dash', Rule::unique($this->service->getTable())->ignore($id)
        ], $rules['code']);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
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
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_check_if_user_is_authorized_to_interact_with_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->WithPermissionsPolicy();
        $restricted = factory(Page::class, 3)->create()->random();
        $page = factory(Page::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $restricted = $this->service->authorize($restricted);
        $authorized = $this->service->authorize($page);

        // Assertions
        $this->assertFalse($restricted);
        $this->assertTrue($authorized);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:page
     * @group  service
     * @group  service:page
     * @return void
     */
    public function it_can_check_if_user_has_unrestricted_authorization_to_interact_with_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.unrestricted']));
        $this->WithPermissionsPolicy();
        $page = factory(Page::class, 3)->create()->random();

        // Actions
        $unrestricted = $this->service->authorize($page);

        // Assertions
        $this->assertTrue($unrestricted);
    }
}
