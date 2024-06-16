<?php

namespace Page\Feature\Api;

use Core\Models\Template;
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
class PagesTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(PageServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_pages()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.index', 'pages.owned']), ['pages.index']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('api.pages.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [[
                    'user_id' => $user->getKey(),
                    'template_id' => $template->getKey(),
                 ]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'title',
                        'template_id',
                        'user_id',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_trashed_pages()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.trashed', 'pages.owned']), ['pages.trashed']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 2)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ]);
        $pages->each->delete();

        // Actions
        $response = $this->get(route('api.pages.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [[
                    'user_id' => $user->getKey(),
                    'template_id' => $template->getKey(),
                 ]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'title',
                        'template_id',
                        'user_id',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_can_visit_their_owned_page_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.show', 'pages.owned']), ['pages.show']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 2)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('api.pages.show', $page->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'user_id' => $user->getKey(),
                    'template_id' => $template->getKey(),
                 ]])
                 ->assertJsonStructure([
                    'data' => [
                        'title',
                        'template_id',
                        'user_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_can_visit_any_page_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.show']), ['pages.show']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 2)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('api.pages.show', $page->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'user_id' => $user->getKey(),
                    'template_id' => $template->getKey(),
                 ]])
                 ->assertJsonStructure([
                    'data' => [
                        'title',
                        'template_id',
                        'user_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_can_store_a_page_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.store']), ['pages.store']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = factory(Page::class)->make([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->toArray();
        $response = $this->post(route('api.pages.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_can_only_update_their_owned_pages()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.owned', 'pages.update']), ['pages.update']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $attributes = factory(Page::class)->make(['template_id' => $template->getKey()])->toArray();
        $response = $this->put(route('api.pages.update', $page->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($page->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_cannot_update_pages_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.owned', 'pages.update']), ['pages.update']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create(['template_id' => $template->getKey()])->random();

        // Actions
        $attributes = factory(Page::class)->make(['template_id' => $template->getKey()])->toArray();
        $response = $this->put(route('api.pages.update', $page->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($page->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_can_only_restore_owned_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.restore', 'pages.owned']), ['pages.restore']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->patch(route('api.pages.restore', $page->getKey()));
        $page = $this->service->find($page->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($page->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_can_only_multiple_restore_owned_pages()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.restore', 'pages.owned']), ['pages.restore']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->patch(route('api.pages.restore'), $attributes);
        $pages = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $pages->each(function ($page) {
            $this->assertFalse($page->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_cannot_restore_page_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.restore', 'pages.owned']), ['pages.restore']);
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $otherUser = $this->asNonSuperAdmin(['pages.owned', 'pages.restore']);
        $page = factory(Page::class, 3)->create([
            'user_id' => $otherUser->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->patch(route('api.pages.restore', $page->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($page->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_cannot_multiple_restore_pages_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.restore', 'pages.owned']), ['pages.restore']);
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $otherUser = $this->asNonSuperAdmin(['pages.owned', 'pages.restore']);
        $pages = factory(Page::class, 3)->create([
            'user_id' => $otherUser->getKey(),
            'template_id' => $template->getKey(),
        ]);

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->patch(route('api.pages.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
        $pages->each(function ($page) {
            $this->assertDatabaseHas($page->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.destroy', 'pages.owned']), ['pages.destroy']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->delete(route('api.pages.destroy', $page->getKey()));
        $page = $this->service->withTrashed()->find($page->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($page->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_pages()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.destroy', 'pages.owned']), ['pages.destroy']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->delete(route('api.pages.destroy', 'null'), $attributes);
        $pages = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $pages->each(function ($page) {
            $this->assertSoftDeleted($page->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_cannot_soft_delete_page_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.destroy', 'pages.owned']), ['pages.destroy']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create(['template_id' => $template->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.pages.destroy', $page->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($page->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_cannot_multiple_soft_delete_pages_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.destroy', 'pages.owned']), ['pages.destroy']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create(['template_id' => $template->getKey()]);

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->delete(route('api.pages.destroy', 'null'), $attributes);

        // Assertions
        $response->assertForbidden();
        $pages->each(function ($page) {
            $this->assertDatabaseHas($page->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.delete', 'pages.owned']), ['pages.delete']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 2)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->delete(route('api.pages.delete', $page->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($page->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_pages()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.delete', 'pages.owned']), ['pages.delete']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->delete(route('api.pages.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $pages->each(function ($page) {
            $this->assertDatabaseMissing($page->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_cannot_permanently_delete_page_owned_by_other_pages()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['pages.delete', 'pages.owned']), ['pages.delete']);
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.pages.delete', $page->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($page->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:page
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_pages_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.delete', 'pages.owned']), ['pages.delete']);
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create(['template_id' => $template->getKey()]);

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->delete(route('api.pages.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
        $pages->each(function ($page) {
            $this->assertDatabaseHas($page->getTable(), $page->toArray());
        });
    }
}
