<?php

namespace Announcement\Feature\Api;

use Announcement\Models\Announcement;
use Announcement\Services\AnnouncementServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Announcement\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AnnouncementsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(AnnouncementServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:announcement
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_announcements()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['announcements.index']), ['announcements.index']);
        $this->withPermissionsPolicy();

        $announcement = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $response = $this->get(route('api.announcements.index'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'title', 'slug', 'body',
                    'photo', 'type', 'user_id',
                    'category_id', 'published_at', 'expired_at',
                    'user', 'template',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:announcements
     * @return void
     */
    public function a_user_can_store_a_announcement_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['announcements.store']), ['announcements.store']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Announcement::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('api.announcements.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('metadata')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:announcement
     * @return void
     */
    public function a_user_can_only_retrieve_an_owned_single_announcement()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['announcements.show']), ['announcements.show']);
        $this->withPermissionsPolicy();

        $announcement = factory(Announcement::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('api.announcements.show', $announcement->getKey()));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'title', 'slug', 'body',
                    'photo', 'type', 'user_id',
                    'category_id', 'published_at', 'expired_at',
                    'user', 'template',
                ],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:announcement
     * @return void
     */
    public function a_user_can_only_update_an_owned_announcement()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['announcements.update']), ['announcements.update']);
        $this->withPermissionsPolicy();

        $announcement = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = factory(Announcement::class)->make()->toArray();

        $response = $this->put(route('api.announcements.update', $announcement->getKey()), $attributes);
        $announcement = $this->service->find($announcement->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($announcement->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:announcement
     * @return void
     */
    public function a_user_can_only_soft_delete_an_owned_announcement()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['announcements.destroy']), ['announcements.destroy']);
        $this->withPermissionsPolicy();

        $announcement = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.announcements.destroy', $announcement->getKey()));
        $announcement = $this->service->withTrashed()->find($announcement->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($announcement->getTable(), $announcement->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:announcement
     * @return void
     */
    public function a_user_can_only_soft_delete_multiple_owned_announcements()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['announcements.destroy']), ['announcements.destroy']);
        $this->withPermissionsPolicy();

        $announcements = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $announcements->pluck('id')->toArray()];
        $response = $this->delete(route('api.announcements.destroy', 'null'), $attributes);
        $announcements = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $announcements->each(function ($announcement) {
            $this->assertSoftDeleted($announcement->getTable(), $announcement->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:announcement
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_trashed_announcements()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['announcements.trashed']), ['announcements.trashed']);
        $this->withPermissionsPolicy();

        $announcements = factory(Announcement::class, 2)->create(['user_id' => $user->getKey()]);
        $announcements->each->delete();

        // Actions
        $response = $this->get(route('api.announcements.trashed'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'title', 'slug', 'body',
                    'photo', 'type', 'user_id',
                    'category_id', 'published_at', 'expired_at',
                    'user', 'template',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:announcement
     * @return void
     */
    public function a_user_can_only_restore_owned_destroyed_announcement()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['announcements.restore']), ['announcements.restore']);
        $this->withPermissionsPolicy();

        $announcement = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()])->random();
        $announcement->delete();

        // Actions
        $response = $this->patch(route('api.announcements.restore', $announcement->getKey()));
        $announcement = $this->service->find($announcement->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($announcement->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:announcements
     * @return void
     */
    public function a_user_can_only_restore_multiple_owned_destroyed_announcements()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['announcements.restore']), ['announcements.restore']);
        $this->withPermissionsPolicy();

        $announcements = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()]);
        $announcements->each->delete();

        // Actions
        $attributes = ['id' => $announcements->pluck('id')->toArray()];
        $response = $this->patch(route('api.announcements.restore'), $attributes);
        $announcements = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $announcements->each(function ($announcement) {
            $this->assertFalse($announcement->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:announcement
     * @return void
     */
    public function a_user_can_only_permanently_delete_multiple_owned_announcements()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['announcements.delete']), ['announcements.delete']);
        $this->withPermissionsPolicy();

        $announcements = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $announcements->pluck('id')->toArray()];
        $response = $this->delete(route('api.announcements.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $announcements->each(function ($announcement) {
            $this->assertDatabaseMissing($announcement->getTable(), $announcement->toArray());
        });
    }
}
