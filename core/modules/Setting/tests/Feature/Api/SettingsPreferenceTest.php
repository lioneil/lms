<?php

namespace Setting\Feature\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Setting\Enumerations\SettingsKey;
use Setting\Services\SettingServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Setting\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SettingsPreferenceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(SettingServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_store_date_settings_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.save']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = [
            SettingsKey::APP_DATE => $value = $this->faker->date(),
        ];
        $response = $this->post(route('api.settings.save'), $attributes);
        $attributes = ['key' => SettingsKey::APP_DATE, 'value' => $value];

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_store_time_settings_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.save']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = [
            SettingsKey::APP_TIME => $value = $this->faker->time(),
        ];
        $response = $this->post(route('api.settings.save'), $attributes);
        $attributes = ['key' => SettingsKey::APP_TIME, 'value' => $value];

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }
}
