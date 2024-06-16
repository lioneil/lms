<?php

namespace Assessment\Feature\Admin;

use Assessment\Models\Assessment;
use Assessment\Models\Field;
use Assessment\Services\SubmissionServiceInterface;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Assessment\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class FieldSubmissionsTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(SubmissionServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:field
     * @group  examinees.index
     * @return void
     */
    public function a_super_user_can_view_all_list_of_examinees()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $assessment = factory(Assessment::class)->create();
        $fields = factory(Field::class, 3)->create(['form_id' => $assessment->getKey()]);

        // Actions
        $response = $this->get(route('examinees.index', $assessment->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('assessment::examinees.index')
                 ->assertSeeText(trans('All Examinees'))
                 ->assertSeeTextInOrder($fields->pluck('title')->toArray())
                 ->assertSeeTextInOrder($fields->pluck('code')->toArray())
                 ->assertSeeTextInOrder($fields->pluck('type')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:field
     * @group  examinees.index
     * @group  user::examinees.index
     * @return void
     */
    public function a_user_can_only_view_their_paginated_list_of_all_examinees()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['examinees.index']));
        $this->withPermissionsPolicy();

        $assessment = factory(Assessment::class)->create();
        $restricted = factory(Field::class, 2)->create(['form_id' => $assessment->getKey()]);
        $fields = factory(Field::class, 2)->create(['form_id' => $assessment->getKey()]);

        // Actions
        $response = $this->get(route('examinees.index', $assessment->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('assessment::examinees.index')
                 ->assertSeeText(trans('All Examinees'));
    }
}
