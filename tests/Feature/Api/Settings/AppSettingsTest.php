<?php

namespace Tests\Feature\Api\Settings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Setting\Services\SettingServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Tests\Feature\Api\Settings
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AppSettingsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(SettingServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:settings
     * @return void
     */
    public function a_user_can_get_the_app_settings()
    {
        // Arrangements
        Passport::actingAs($user = $this->superAdmin, ['settings/app']);

        $this->service->set([
            'app:title' => $title = $this->faker->sentence(),
            'app:year' => $year = date('Y'),
            'app:theme' => 'default',
        ])->save();

        // Actions
        $response = $this->get(route('api.settings.app'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson([
                    'app:title' => $title,
                    'app:year' => $year,
                    'app:theme' => 'default',
                 ]);
    }
}
