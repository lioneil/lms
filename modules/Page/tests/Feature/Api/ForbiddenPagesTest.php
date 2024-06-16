<?php

namespace Page\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Page\Models\Page;
use Page\Services\PageServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Page\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ForbiddenPagesTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(PageServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @group  feature:api:page:forbidden
     * @return void
     */
    public function an_upermitted_user_cannot_retrieve_the_paginated_list_of_all_pages()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->post(route('api.pages.index'));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @group  feature:api:page:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_store_a_page_to_database()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Page::class)->make()->toArray();
        $response = $this->post(route('api.pages.store'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @group  feature:api:page:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_a_single_page()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 2)->create()->random();

        // Actions
        $response = $this->get(route('api.pages.show', $page->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @group  feature:api:page:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_update_a_page()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 2)->create()->random();

        // Actions
        $attributes = factory(Page::class)->make()->toArray();
        $response = $this->put(route('api.pages.update', $page->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @group  feature:api:page:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_soft_delete_a_page()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.pages.destroy', $page->getKey()));
        $page = $this->service->withTrashed()->find($page->getKey());

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @group  feature:api:page:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_soft_delete_multiple_pages()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $pages = factory(Page::class, 3)->create();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->delete(route('api.pages.destroy', 'null'), $attributes);
        $pages = $this->service->onlyTrashed();

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @group  feature:api:page:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_the_paginated_list_of_all_trashed_pages()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $pages = factory(Page::class, 2)->create();
        $pages->each->delete();

        // Actions
        $response = $this->get(route('api.pages.trashed'));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @group  feature:api:page:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_restore_destroyed_pages()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 3)->create()->random();
        $page->delete();

        // Actions
        $response = $this->patch(route('api.pages.restore', $page->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @group  feature:api:page:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_restore_multiple_destroyed_pages()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $pages = factory(Page::class, 3)->create();
        $pages->each->delete();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->patch(route('api.pages.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @group  feature:api:page:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_permanently_delete_a_page()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.pages.delete', $page->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @group  feature:api:page:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_permanently_delete_multiple_pages()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $pages = factory(Page::class, 3)->create();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->delete(route('api.pages.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
    }
}
