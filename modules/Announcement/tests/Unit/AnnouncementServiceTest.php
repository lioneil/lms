<?php

namespace Announcement\Unit;

use Announcement\Models\Announcement;
use Announcement\Services\AnnouncementServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Announcement\Unit
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AnnouncementServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /* Set up the service class*/
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(AnnouncementServiceInterface::class);
    }

    /**
     * A basic test example.
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_should_return_the_url_when_uploading_the_file()
    {
        //Arrangements
        $fakeFile = UploadedFile::fake()->create('tmp.txt', 20);

        //Actions
        $actual = $this->service->upload($fakeFile);

        //Assertions
        $this->assertIsString($actual);
    }

    /**
     * A basic test example.
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_upload_a_file_to_storage()
    {
        //Arrangements
        $file = UploadedFile::fake()->create('image.png');

        //Action
        $actual = $this->service->upload($file);
        $path = $this->service->getPathname();

        //Assertions
        File::exists($path);
        $this->assertTrue(file_exists($path));
        $this->assertTrue(is_file($path));
        $this->assertTrue(is_string($path));
    }

    /**
     * Browse
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_return_a_paginated_list_of_announcements()
    {
        // Arrangements
        $announcements = factory(Announcement::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * Browse
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_announcements()
    {
        // Arrangements
        $announcements = factory(Announcement::class, 10)->create();

        // Actions
        $actual = $this->service->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * Read
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_find_and_return_an_existing_announcement()
    {
        // Arrangements
        $expected = factory(Announcement::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Announcement::class, $actual);
    }

    /**
     * Read
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_will_abort_to_404_when_a_announcement_does_not_exist()
    {
        // Arrangements
        factory(Announcement::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * Edit
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_update_an_existing_announcement()
    {
         // Arrangements
        $announcement = factory(Announcement::class)->create();

        // Actions
        $attributes = [
            'title' => $title = $this->faker->unique()->words(10, true),
        ];
        $actual = $this->service->update($announcement->getKey(), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $this->assertTrue($actual);
    }

    /**
     * Edit
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_restore_a_soft_deleted_announcement()
    {
        // Arrangements
        $announcement = factory(Announcement::class)->create();
        $announcement->delete();

        // Actions
        $actual = $this->service->restore($announcement->getKey());
        $restored = $this->service->find($announcement->getKey());

        // Assertions
        $this->assertNull($actual);
        $this->assertNull($restored->deleted_at);
    }

    /**
     * Edit
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_restore_multiple_soft_deleted_announcements()
    {
        // Arrangements
        $announcements = factory(Announcement::class, 5)->create();
        $announcements->each->delete();

        // Actions
        $actual = $this->service->restore($announcements->pluck('id')->toArray());

        // Assertions
        $this->assertNull($actual);
        $announcements->each(function ($announcement) {
            $restored = $this->service->find($announcement->getKey());
            $this->assertNull($restored->deleted_at);
        });
    }

    /**
     * Add
     *
     * @test
     * @group  unit
     * @group  unit:Announcement
     * @group  service
     * @group  service:Announcement
     * @return void
     */
    public function it_can_store_a_Announcement_to_database()
    {
        // Arrangements
        $attributes = factory(Announcement::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * Delete
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_soft_delete_an_existing_announcement()
    {
        // Arrangement
        $announcement = factory(Announcement::class, 3)->create()->random();

        // Actions
        $this->service->destroy($announcement->getKey());

        // Assertions
        $this->assertSoftDeleted($this->service->getTable(), $announcement->toArray());
    }

    /**
     * Delete
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_soft_delete_multiple_existing_announcements()
    {
        // Arrangements
        $announcements = factory(Announcement::class, 3)->create();

        // Actions
        $this->service->destroy($announcements->pluck('id')->toArray());

        // Assertions
        $announcements->each(function ($announcement) {
            $this->assertSoftDeleted($this->service->getTable(), $announcement->toArray());
        });
    }

    /**
     * Delete
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_announcement()
    {
        // Arrangements
        $announcement = factory(Announcement::class)->create();
        $announcement->delete();

        // Actions
        $this->service->delete($announcement->getKey());

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $announcement->toArray());
    }

    /**
     * Delete
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_permanently_delete_multiple_soft_deleted_announcements()
    {
        // Arrangements
        $announcements = factory(Announcement::class, 5)->create();
        $announcements->each->delete();

        // Actions
        $this->service->delete($announcements->pluck('id')->toArray());

        // Assertions
        $announcements->each(function ($announcement) {
            $this->assertDatabaseMissing($this->service->getTable(), $announcement->toArray());
        });
    }

    /**
     * Rules
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules($id = 1);

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertEquals('required|max:255', $rules['title']);
        $this->assertEquals('required|numeric', $rules['user_id']);
    }

    /**
     * Authorization
     *
     * @test
     * @group  unit
     * @group  unit:announcement
     * @group  service
     * @group  service:announcement
     * @return void
     */
    public function it_can_check_if_user_is_authorized_to_interact_with_announcements()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->withPermissionsPolicy();
        $restricted = factory(Announcement::class, 3)->create()->random();
        $announcement = factory(Announcement::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $restricted = $this->service->authorize($restricted);
        $authorized = $this->service->authorize($announcement);

        // Assertions
        $this->assertFalse($restricted);
        $this->assertTrue($authorized);
    }
}
