<?php

namespace Tests\Assessment\Unit;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Assessment\Models\Assessment;
use Assessment\Services\AssessmentServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use Illuminate\Validation\Rule;
use User\Models\User;

/**
 * @package Assessment\Unit
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AssessmentServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

     /* Set up the service class*/
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(AssessmentServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_can_return_a_paginated_list_of_assessments()
    {
        // Arrangements
        $materials = factory(Assessment::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_assessments()
    {
        // Arrangements
        $materials = factory(Assessment::class, 10)->create();

        // Actions
        $actual = $this->service->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_can_find_and_return_an_existing_assessment()
    {
        // Arrangements
        $expected = factory(Assessment::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Assessment::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_will_abort_to_404_when_a_assessment_does_not_exist()
    {
        // Arrangements
        factory(Assessment::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_can_update_an_existing_assessment()
    {
        // Arrangements
        $assessment = factory(Assessment::class)->create();

        // Actions
        $attributes = [
            'title' => $title = $this->faker->unique()->words(10, true),
        ];

        $attributes['uri'] = UploadedFile::fake()->create('tmp.text');
        $actual = $this->service->update($assessment->getKey(), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('uri', 'pathname')->
            toArray());
        $this->assertTrue($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_can_restore_a_soft_deleted_assessment()
    {
        // Arrangements
        $assessment = factory(Assessment::class)->create();
        $assessment->delete();

        // Actions
        $actual = $this->service->restore($assessment->getKey());
        $restored = $this->service->find($assessment->getKey());

        // Assertions
        $this->assertNull($actual);
        $this->assertNull($restored->deleted_at);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_can_restore_multiple_soft_deleted_assessments()
    {
        // Arrangements
        $assessments = factory(Assessment::class, 5)->create();
        $assessments->each->delete();

        // Actions
        $actual = $this->service->restore($assessments->pluck('id')->toArray());

        // Assertions
        $this->assertNull($actual);
        $assessments->each(function ($assessment) {
            $restored = $this->service->find($assessment->getKey());
            $this->assertNull($restored->deleted_at);
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_can_store_a_assessment_to_database()
    {
        // Arrangements
        $attributes = factory(Assessment::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('metadata')->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_can_soft_delete_an_existing_assessment()
    {
        // Arrangements
        $assessment = factory(Assessment::class, 3)->create()->random();

        // Actions
        $this->service->destroy($assessment->getKey());
        $assessment = $this->service->withTrashed()->find($assessment->getKey());

        // Assertions
        $this->assertSoftDeleted($this->service->getTable(), collect($assessment)->except('metadata')->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_can_soft_delete_multiple_existing_assessments()
    {
        // Arrangements
        $assessments = factory(Assessment::class, 3)->create();

        // Actions
        $this->service->destroy($assessments->pluck('id')->toArray());
        $assessments = $this->service->withTrashed()->whereIn(
            'id', $assessments->pluck('id')->toArray()
        );

        // Assertions
        $assessments->each(function ($assessment) {
            $this->assertSoftDeleted($this->service->getTable(), collect($assessment)->except('metadata')->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_assessment()
    {
        // Arrangements
        $assessment = factory(Assessment::class)->create();
        $assessment->delete();

        // Actions
        $this->service->delete($assessment->getKey());

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $assessment->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_can_permanently_delete_multiple_soft_deleted_assessments()
    {
        // Arrangements
        $assessments = factory(Assessment::class, 5)->create();
        $assessments->each->delete();

        // Actions
        $this->service->delete($assessments->pluck('id')->toArray());

        // Assertions
        $assessments->each(function ($assessment) {
            $this->assertDatabaseMissing($this->service->getTable(), $assessment->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
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
         $this->assertEquals('required|max:255', $rules['subtitle']);
        $this->assertEquals('required|numeric', $rules['user_id']);
    }


    /**
     * @test
     * @group  unit
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
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
     * @group  unit:assessment
     * @group  service
     * @group  service:assessment
     * @return void
     */
    public function it_can_check_if_user_is_authorized_to_interact_with_assessments()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->withPermissionsPolicy();
        $restricted = factory(Assessment::class, 3)->create()->random();
        $assessment = factory(Assessment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $restricted = $this->service->authorize($restricted);
        $authorized = $this->service->authorize($assessment);

        // Assertions
        $this->assertFalse($restricted);
        $this->assertTrue($authorized);
    }
}
