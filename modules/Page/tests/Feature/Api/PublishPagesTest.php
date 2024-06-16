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
class PublishPagesTest extends TestCase
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
     * @group  feature:page
     * @group  pages.publish
     * @group  user:pages.publish
     * @return void
     */
    public function a_user_can_publish_owned_page()
    {
        // Arranements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.publish']));
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->post(route('api.pages.publish', $page->getKey()));
        $page = $this->service->find($page->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($page->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.publish
     * @group  user:pages.publish
     * @return void
     */
    public function a_user_cannot_publish_others_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.publish']));
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 3)->create()->random();

        // Actions
        $response = $this->post(route('api.pages.publish', $page->getKey()));
        $page = $this->service->find($page->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($page->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.unpublish
     * @group  user:pages.unpublish
     * @return void
     */
    public function a_user_can_unpublish_a_published_owned_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.unpublish']));
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();
        $page->publish();

        // Actions
        $response = $this->post(route('api.pages.unpublish', $page->getKey()));
        $page = $this->service->find($page->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($page->isUnpublished());
        $this->assertFalse($page->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.unpublish
     * @group  user:pages.unpublish
     * @return void
     */
    public function a_user_cannot_unpublish_others_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.unpublish']));
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 3)->create()->random();
        $page->publish();

        // Actions
        $response = $this->post(route('api.pages.unpublish', $page->getKey()));
        $page = $this->service->find($page->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($page->isUnpublished());
        $this->assertTrue($page->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.draft
     * @group  user:pages.draft
     * @return void
     */
    public function a_user_can_draft_owned_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.draft']));
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->post(route('api.pages.draft', $page->getKey()));
        $page = $this->service->find($page->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($page->isPublished());
        $this->assertTrue($page->isDrafted());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.draft
     * @group  user:pages.draft
     * @return void
     */
    public function a_user_cannot_draft_others_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.draft']));
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 2)->create()->random();

        // Actions
        $response = $this->post(route('api.pages.draft', $page->getKey()));
        $page = $this->service->find($page->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($page->isDrafted());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.expire
     * @group  user:pages.expire
     * @return void
     */
    public function a_user_can_expire_owned_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.expire']));
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->patch(route('api.pages.expire', $page->getKey()));
        $page = $this->service->find($page->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($page->isExpired());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:page
     * @group  pages.expire
     * @group  user:pages.expire
     * @return void
     */
    public function a_user_cannot_expire_others_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['pages.expire']));
        $this->withPermissionsPolicy();
        $page = factory(Page::class, 2)->create()->random();

        // Actions
        $response = $this->patch(route('api.pages.expire', $page->getKey()));
        $page = $this->service->find($page->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($page->isExpired());
    }
}
