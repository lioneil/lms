<?php

namespace Page\Feature;

use Core\Application\Permissions\PermissionsPolicy;
use Core\Models\Template;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Page\Models\Page;
use Page\Services\PageServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Page\Feature
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PagesTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp() : void
    {
        parent::setUp();

        $this->service = $this->app->make(PageServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.index
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 5)->create(['template_id' => $template->getKey()]);

        // Actions
        $response = $this->get(route('pages.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('page::admin.index')
                 ->assertSeeText(trans('Add Page'))
                 ->assertSeeText(trans('All Pages'))
                 ->assertSeeTextInOrder($pages->pluck('title')->toArray())
                 ->assertSeeTextInOrder($pages->pluck('code')->toArray())
                 ->assertSeeTextInOrder($pages->pluck('feature')->toArray())
                 ->assertSeeTextInOrder($pages->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.trashed
     * @return void
     */
    public function a_super_user_can_view_a_paginated_list_of_all_trashed_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 5)->create(['template_id' => $template->getKey()]);
        $pages->each->delete();

        // Actions
        $response = $this->get(route('pages.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('page::admin.trashed')
                 ->assertSeeText(trans('Back to Pages'))
                 ->assertSeeText(trans('Archived Pages'))
                 ->assertSeeTextInOrder($pages->pluck('title')->toArray())
                 ->assertSeeTextInOrder($pages->pluck('code')->toArray())
                 ->assertSeeTextInOrder($pages->pluck('feature')->toArray())
                 ->assertSeeTextInOrder($pages->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertSeeTextInOrder([trans('Restore'), trans('Remove Permanently')]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.show
     * @return void
     */
    public function a_super_user_can_visit_an_page_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 4)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('pages.show', $page->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('page::admin.show')
                 ->assertSeeText($page->title)
                 ->assertSeeText($page->code)
                 ->assertSeeText($page->feature);
        $this->assertEquals($page->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.edit
     * @return void
     */
    public function a_super_user_can_visit_the_edit_page_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create();
        $page = factory(Page::class, 3)->create(['template_id' => $template->getKey()])->random();

        // Actions
        $response = $this->get(route('pages.edit', $page->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewHas('resource')
                 ->assertViewIs('page::admin.edit')
                 ->assertSeeText(trans('Edit Page'))
                 ->assertSeeText($page->title)
                 ->assertSeeText($page->code)
                 ->assertSeeText($page->feature)
                 ->assertSeeText(trans('Update Page'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.update
     * @return void
     */
    public function a_super_user_can_update_an_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create();
        $page = factory(Page::class, 3)->create(['template_id' => $template->getKey()])->random();

        // Actions
        $attributes = factory(Page::class)->make()->toArray();
        $response = $this->put(route('pages.update', $page->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('pages.show', $page->getKey()));
        $this->assertDatabaseHas($page->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.restore
     * @return void
     */
    public function a_super_user_can_restore_an_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create(['user_id'  => $user->getKey()]);
        $page = factory(Page::class, 3)->create(['template_id' => $template->getKey()])->random();
        $page->delete();

        // Actions
        $response = $this->patch(
            route('pages.restore', $page->getKey()), [], ['HTTP_REFERER' => route('pages.trashed')]
        );
        $page = $this->service->find($page->getKey());

        // Assertions
        $response->assertRedirect(route('pages.trashed'));
        $this->assertFalse($page->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.restore
     * @return void
     */
    public function a_super_user_can_restore_multiple_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create(['template_id' => $template->getKey()]);
        $pages->each->delete();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->patch(
            route('pages.restore'), $attributes, ['HTTP_REFERER' => route('pages.trashed')]
        );
        $pages = $this->service->whereIn('id', $pages->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('pages.trashed'));
        $pages->each(function ($page) {
            $this->assertFalse($page->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.create
     * @return void
     */
    public function a_super_user_can_visit_the_create_page_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);

        // Actions
        $response = $this->get(route('pages.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('page::admin.create')
                 ->assertSeeText(trans('Create Page'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('Code'))
                 ->assertSeeText(trans('Feature'))
                 ->assertSeeText(trans('Save Page'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.store
     * @return void
     */
    public function a_super_user_can_store_an_page_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create();

        // Actions
        $attributes = factory(Page::class)->make([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->toArray();
        $response = $this->post(route('pages.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('pages.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_an_page()
    {
        // Arrangement
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create(['user_id' => $user->getKey(), 'template_id' => $template->getKey()])->random();

        // Actions
        $response = $this->delete(
            route('pages.destroy', $page->getKey()), [], ['HTTP_REFERER' => route('pages.index')]
        );
        $page = $this->service->withTrashed()->find($page->getKey());

        // Assertions
        $response->assertRedirect(route('pages.index'));
        $this->assertSoftDeleted($page->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.destroy
     * @return void
     */
    public function a_super_user_can_soft_delete_multiple_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create(['template_id' => $template->getKey()]);

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->delete(route('pages.destroy', $single = 'false'), $attributes);
        $pages = $this->service->withTrashed()->whereIn('id', $pages->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('pages.index'));
        $pages->each(function ($page) {
            $this->assertSoftDeleted($page->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_an_page()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create(['template_id' => $template->getKey()])->random();
        $page->delete();

        // Actions
        $response = $this->delete(
            route('pages.delete', $page->getKey()), [],
            ['HTTP_REFERER' => route('pages.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('pages.trashed'));
        $this->assertDatabaseMissing($page->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.delete
     * @return void
     */
    public function a_super_user_can_permanently_delete_multiple_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create(['template_id' => $template->getKey()]);
        $pages->each->delete();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->delete(
            route('pages.delete'), $attributes, ['HTTP_REFERER' => route('pages.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('pages.trashed'));
        $pages->each(function ($page) {
            $this->assertDatabaseMissing($page->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.index
     * @group  user::pages.index
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.index', 'pages.owned']));
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $restricted = factory(Page::class, 2)->create(['template_id' => $template->getKey()]);
        $pages = factory(Page::class, 2)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ]);

        // Actions
        $response = $this->get(route('pages.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('page::admin.index')
                 ->assertSeeText(trans('All Pages'))
                 ->assertSeeTextInOrder($pages->pluck('title')->toArray())
                 ->assertSeeTextInOrder($pages->pluck('code')->toArray())
                 ->assertSeeTextInOrder($pages->pluck('feature')->toArray())
                 ->assertSeeTextInOrder($pages->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->title)
                 ->assertDontSeeText($restricted->random()->code)
                 ->assertDontSeeText($restricted->random()->feature);
        $this->assertSame($pages->random()->author, $user->displayname);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.trashed
     * @group  user::pages.trashed
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_all_trashed_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.trashed', 'pages.owned']));
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $restricted = factory(Page::class, 2)->create(['template_id' => $template->getKey()]);
        $restricted->each->delete();
        $pages = factory(Page::class, 2)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ]);
        $pages->each->delete();

        // Actions
        $response = $this->get(route('pages.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('page::admin.trashed')
                 ->assertSeeText(trans('Back to Page'))
                 ->assertSeeText(trans('Archived Page'))
                 ->assertSeeTextInOrder($pages->pluck('title')->toArray())
                 ->assertSeeTextInOrder($pages->pluck('code')->toArray())
                 ->assertSeeTextInOrder($pages->pluck('feature')->toArray())
                 ->assertSeeTextInOrder($pages->pluck('author')->map(function ($author) {
                    return e($author);
                 })->toArray())
                 ->assertDontSeeText($restricted->random()->title)
                 ->assertDontSeeText($restricted->random()->code)
                 ->assertDontSeeText($restricted->random()->feature)
                 ->assertDontSeeText($restricted->random()->author);
        $this->assertSame($pages->random()->author, $user->displayname);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.owned
     * @group  user:pages.owned
     * @return void
     */
    public function a_user_can_visit_owned_pages_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'pages.edit', 'pages.show', 'pages.owned', 'pages.destroy'
        ]));
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('pages.show', $page->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('page::admin.show')
                 ->assertSeeText($page->title)
                 ->assertSeeText($page->code)
                 ->assertSeeText($page->feature)
                 ->assertSeeTextInOrder([trans('Edit'), trans('Move to Trash')]);
        $this->assertEquals($page->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.show
     * @group  user:pages.show
     * @return void
     */
    public function a_user_can_visit_any_page_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'pages.edit', 'pages.show', 'pages.owned', 'pages.destroy'
        ]));
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 4)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('pages.show', $page->getKey()));
        $actual = $response->original->resource;

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('page::admin.show')
                 ->assertSeeText($page->title)
                 ->assertSeeText($page->code)
                 ->assertSeeText($page->feature);
        $this->assertEquals($page->getKey(), $actual->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.show
     * @group  user:pages.show
     * @return void
     */
    public function a_user_cannot_edit_others_pages_on_the_show_page_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([
            'pages.edit', 'pages.show', 'pages.owned', 'pages.destroy'
        ]));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'pages.edit', 'pages.show', 'pages.owned', 'pages.destroy'
        ]);

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $otherUser->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('pages.show', $page->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('page::admin.show')
                 ->assertDontSeeText(trans('Edit'))
                 ->assertDontSeeText(trans('Move to Trash'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.edit
     * @group  user:pages.edit
     * @return void
     */
    public function a_user_can_only_visit_their_owned_edit_page_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.edit', 'pages.update']));
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('pages.edit', $page->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.edit
     * @group  user:pages.edit
     * @return void
     */
    public function a_user_cannot_visit_others_edit_page_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.edit', 'pages.update', 'pages.owned']));
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create(['template_id' => $template->getKey()])->random();

        // Actions
        $response = $this->get(route('pages.edit', $page->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.update
     * @group  user:pages.update
     * @return void
     */
    public function a_user_can_only_update_their_owned_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.owned', 'pages.update']));
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $attributes = factory(Page::class)->make()->toArray();
        $response = $this->put(route('pages.update', $page->getKey()), $attributes);

        // Assertions
        $response->assertRedirect(route('pages.show', $page->getKey()));
        $this->assertDatabaseHas($page->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.update
     * @group  user:pages.update
     * @return void
     */
    public function a_user_cannot_update_others_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.owned', 'pages.update']));
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create(['template_id' => $template->getKey()])->random();

        // Actions
        $attributes = ['title' => $this->faker->words($count = 5, $asText = true)];
        $response = $this->put(route('pages.update', $page->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($page->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.restore
     * @group  user:pages.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.owned', 'pages.restore']));
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();
        $page->delete();

        // Actions
        $response = $this->patch(
            route('pages.restore', $page->getKey()), [], ['HTTP_REFERER' => route('pages.trashed')]
        );
        $page = $this->service->find($page->getKey());

        // Assertions
        $response->assertRedirect(route('pages.trashed'));
        $this->assertFalse($page->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.restore
     * @group  user:pages.restore
     * @return void
     */
    public function a_user_can_only_restore_owned_multiple_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.owned', 'pages.restore']));
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ]);
        $pages->each->delete();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->patch(
            route('pages.restore'), $attributes, ['HTTP_REFERER' => route('pages.trashed')]
        );
        $pages = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertRedirect(route('pages.trashed'));
        $pages->each(function ($page) {
            $this->assertFalse($page->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.restore
     * @group  user:pages.restore
     * @return void
     */
    public function a_user_cannot_restore_others_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.owned', 'pages.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['pages.owned', 'pages.restore']);
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $otherUser->getKey(),
            'template_id' => $template->getKey(),
        ])->random();
        $page->delete();

        // Actions
        $response = $this->patch(
            route('pages.restore', $page->getKey()), [], ['HTTP_REFERER' => route('pages.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($page->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.restore
     * @group  user:pages.restore
     * @return void
     */
    public function a_user_cannot_restore_others_multiple_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.owned', 'pages.restore']));
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['pages.owned', 'pages.restore']);
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create([
            'user_id' => $otherUser->getKey(),
            'template_id' => $template->getKey(),
        ]);
        $pages->each->delete();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->patch(
            route('pages.restore'), $attributes, ['HTTP_REFERER' => route('pages.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $pages->each(function ($page) {
            $this->assertDatabaseHas($page->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.create
     * @group  user:pages.create
     * @return void
     */
    public function a_user_can_visit_the_create_page_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.create']));
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->get(route('pages.create'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('service')
                 ->assertViewIs('page::admin.create')
                 ->assertSeeText(trans('Create Page'))
                 ->assertSeeText(trans('Title'))
                 ->assertSeeText(trans('Code'))
                 ->assertSeeText(trans('Feature'))
                 ->assertSeeText(trans('Save Page'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.store
     * @group  user:pages.store
     * @return void
     */
    public function a_user_can_store_an_page_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.create', 'pages.store']));
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = factory(Page::class)->make([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->toArray();
        $response = $this->post(route('pages.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('pages.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.destroy
     * @group  user:pages.destroy
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.destroy', 'pages.owned']));
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->delete(route('pages.destroy', $page->getKey()));

        // Assertions
        $response->assertRedirect(route('pages.index'));
        $this->assertSoftDeleted($page->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.destroy
     * @group  user:pages.destroy
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.index', 'pages.destroy', 'pages.owned']));
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 2)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ]);

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->delete(
            route('pages.destroy', '@null'), $attributes, ['HTTP_REFERER' => route('pages.index')]
        );
        $pages = $this->service->withTrashed()->whereIn('id', $pages->pluck('id')->toArray())->get();

        // Assertions
        $response->assertRedirect(route('pages.index'));
        $pages->each(function ($page) {
            $this->assertSoftDeleted($page->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.destroy
     * @group  user:pages.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_others_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.destroy', 'pages.owned']));
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create(['template_id' => $template->getKey()])->random();

        // Actions
        $response = $this->delete(route('pages.destroy', $page->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($page->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.destroy
     * @group  user:pages.destroy
     * @return void
     */
    public function a_user_cannot_soft_delete_multiple_others_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.trashed', 'pages.destroy', 'pages.owned']));
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create(['template_id' => $template->getKey()]);
        $pages->each->delete();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->delete(
            route('pages.destroy', '@null'), $attributes, ['HTTP_REFERER' => route('pages.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $pages->each(function ($page) {
            $this->assertDatabaseHas($page->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.destroy
     * @group  user:pages.destroy
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.trashed', 'pages.delete', 'pages.owned']));
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();
        $page->delete();

        // Actions
        $response = $this->delete(
            route('pages.delete', $page->getKey()), [], ['HTTP_REFERER' => route('pages.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('pages.trashed'));
        $this->assertDatabaseMissing($page->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.delete
     * @group  user:pages.delete
     * @return void
     */
    public function a_user_can_only_multiple_permanetly_delete_owned_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.trashed', 'pages.delete', 'pages.owned']));
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ]);
        $pages->each->delete();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->delete(
            route('pages.delete'), $attributes, ['HTTP_REFERER' => route('pages.trashed')]
        );

        // Assertions
        $response->assertRedirect(route('pages.trashed'));
        $pages->each(function ($page) {
            $this->assertDatabaseMissing($page->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.delete
     * @group  user:pages.delete
     * @return void
     */
    public function a_user_cannot_permanently_delete_others_page()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.trashed', 'pages.delete', 'pages.owned']));
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create(['template_id' => $template->getKey()])->random();
        $page->delete();

        // Actions
        $response = $this->delete(
            route('pages.delete', $page->getKey()), [], ['HTTP_REFERER' => route('pages.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($page->getTable(), $page->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.delete
     * @group  user:pages.delete
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_others_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.trashed', 'pages.delete', 'pages.owned']));
        $this->withPermissionsPolicy();
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $pages = factory(Page::class, 3)->create(['template_id' => $template->getKey()]);
        $pages->each->delete();

        // Actions
        $attributes = ['id' => $pages->pluck('id')->toArray()];
        $response = $this->delete(
            route('pages.delete'), $attributes, ['HTTP_REFERER' => route('pages.trashed')]
        );

        // Assertions
        $response->assertForbidden();
        $pages->each(function ($page) {
            $this->assertDatabaseHas($page->getTable(), $page->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.preview
     * @group  user:pages.preview
     * @return void
     */
    public function a_super_user_can_only_preview_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('pages.preview', $page->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('page::admin.preview')
                 ->assertSeeText(trans('Preview Page'))
                 ->assertSeeTextInOrder([trans('Continue Editing'), trans('Move to Trash')])
                 ->assertSeeText($page->title)
                 ->assertSeeText($page->code);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.preview
     * @group  user:pages.preview
     * @return void
     */
    public function a_super_user_can_preview_others_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $this->WithPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create(['template_id' => $template->getKey()])->random();

        // Actions
        $response = $this->get(route('pages.preview', $page->getKey()));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.preview
     * @group  user:pages.preview
     * @return void
     */
    public function a_user_can_only_preview_owned_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.preview', 'pages.owned']));
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey(),
            'template_id' => $template->getKey(),
        ])->random();

        // Actions
        $response = $this->get(route('pages.preview', $page->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('page::admin.preview')
                 ->assertSeeText(trans('Preview Page'))
                 ->assertSeeText($page->title)
                 ->assertSeeText($page->code);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.preview
     * @group  user:pages.preview
     * @return void
     */
    public function a_user_cannot_preview_others_pages()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['pages.preview', 'pages.owned']));
        $this->withPermissionsPolicy();

        $template = factory(Template::class)->create(['user_id' => $user->getKey()]);
        $page = factory(Page::class, 3)->create(['template_id' => $template->getKey()])->random();

        // Actions
        $response = $this->get(route('pages.preview', $page->getKey()));

        // Assertions
        $response->assertForbidden();
    }
}
