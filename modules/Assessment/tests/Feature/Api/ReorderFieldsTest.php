<?php

namespace Assessment\Feature\Api;

use Assessment\Models\Assessment;
use Assessment\Models\Field;
use Assessment\Services\FieldServiceInterface;
use Course\Models\Content;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Assessment\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ReorderFieldsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(FieldServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }
    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:field:reorder
     * @return void
     */
    public function a_user_can_reorder_contents()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['fields.reorder']), ['fields.reorder']);
        $this->withPermissionsPolicy();

        $assessment = factory(Assessment::class)->create();
        $fields = factory(Field::class, 4)->create([
            'sort' => 0,
            'form_id' => $assessment->getKey(),
        ])->map->only('id', 'sort');

        // Actions
        $attributes = $fields->map(function ($field) {
            return array_merge($field, [
                'sort' => $this->faker->randomDigitNotNull(),
            ]);
        });

        $attributes = ['fields' => $attributes->toArray()];
        $response = $this->patch(route('api.fields.reorder', $attributes));
        $field = $this->service->find($fields->random()['id']);

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($field->sort == 0);
    }
}
