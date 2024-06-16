<?php

namespace Assessment\Unit;

use Assessment\Models\Submission;
use Assessment\Services\SubmissionServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Assessment\Unit
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SubmissionServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(SubmissionServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:submission
     * @group  service
     * @group  service:submission
     * @return void
     */
    public function it_can_return_a_paginated_list_of_submissions()
    {
        // Arrangements
        $submissions = factory(Submission::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:submission
     * @group  service
     * @group  service:submission
     * @return void
     */
    public function it_can_find_and_return_an_existing_submission()
    {
        // Arrangements
        $expected = factory(Submission::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Submission::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:submission
     * @group  service
     * @group  service:submission
     * @return void
     */
    public function it_will_abort_to_404_when_a_submission_does_not_exist()
    {
        // Arrangements
        factory(Submission::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:submission
     * @group  service
     * @group  service:submission
     * @return void
     */
    public function it_can_update_an_existing_submission()
    {
        // Arrangements
        $submission = factory(Submission::class)->create();

        // Actions
        $attributes = [
            'remarks' => $this->faker->words($nb = 3, $asText = true),
        ];
        $actual = $this->service->update($submission->getKey(), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $this->assertTrue($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:submission
     * @group  service
     * @group  service:submission
     * @return void
     */
    public function it_can_store_a_submission_to_database()
    {
        // Arrangements
        $attributes = factory(Submission::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:submission
     * @group  service
     * @group  service:submission
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules($id = 1);

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('results', $rules);
        $this->assertArrayHasKey('submissible_id', $rules);
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertEquals('required|max:255', $rules['results']);
        $this->assertEquals('required|numeric', $rules['submissible_id']);
        $this->assertEquals('required|numeric', $rules['user_id']);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:submission
     * @group  service
     * @group  service:submission
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
     * @group  unit:submission
     * @group  service
     * @group  service:submission
     * @return void
     */
    public function it_can_check_if_user_is_authorized_to_interact_with_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->WithPermissionsPolicy();
        $restricted = factory(Submission::class, 3)->create()->random();
        $submission = factory(Submission::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $restricted = $this->service->authorize($restricted);
        $authorized = $this->service->authorize($submission);

        // Assertions
        $this->assertFalse($restricted);
        $this->assertTrue($authorized);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:submission
     * @group  service
     * @group  service:submission
     * @return void
     */
    public function it_can_check_if_user_has_unrestricted_authorization_to_interact_with_submissions()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['submissions.unrestricted']));
        $this->WithPermissionsPolicy();
        $submission = factory(Submission::class, 3)->create()->random();

        // Actions
        $unrestricted = $this->service->authorize($submission);

        // Assertions
        $this->assertTrue($unrestricted);
    }
}
