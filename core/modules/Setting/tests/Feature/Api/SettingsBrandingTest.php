<?php

namespace Setting\Feature\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
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
class SettingsBrandingTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(SettingServiceInterface::class);
    }

    /** Tear down the test class */
    public function tearDown(): void
    {
        if (file_exists(public_path('testlogo.png'))) {
            File::delete(public_path('testlogo.png'));
        }

        parent::tearDown();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_update_app_title()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.save', 'settings.branding']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = [
            SettingsKey::APP_TITLE => $value = $this->faker->words($nb = 4, $asText = true),
        ];
        $response = $this->post(route('api.settings.save'), $attributes);
        $attributes = ['key' => SettingsKey::APP_TITLE, 'value' => $value];

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
    public function a_user_can_update_app_author()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.save', 'settings.branding']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = [
            SettingsKey::APP_AUTHOR => $value = $this->faker->name(),
        ];
        $response = $this->post(route('api.settings.save'), $attributes);
        $attributes = ['key' => SettingsKey::APP_AUTHOR, 'value' => $value];

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
    public function a_user_can_update_app_email()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.save', 'settings.branding']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = [
            SettingsKey::APP_EMAIL => $value = $this->faker->email(),
        ];
        $response = $this->post(route('api.settings.save'), $attributes);
        $attributes = ['key' => SettingsKey::APP_EMAIL, 'value' => $value];

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
    public function a_user_can_update_app_year()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.save', 'settings.branding']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = [
            SettingsKey::APP_YEAR => $value = $this->faker->email(),
        ];
        $response = $this->post(route('api.settings.save'), $attributes);
        $attributes = ['key' => SettingsKey::APP_YEAR, 'value' => $value];

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
    public function a_user_can_update_app_theme()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.save', 'settings.branding']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = [
            SettingsKey::APP_THEME => $value = $this->faker->word(),
        ];
        $response = $this->post(route('api.settings.save'), $attributes);
        $attributes = ['key' => SettingsKey::APP_THEME, 'value' => $value];

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
    public function a_user_can_update_app_copyright()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.save', 'settings.branding']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = [
            SettingsKey::APP_COPYRIGHT => $value = $this->faker->words($nb = 5, $asText = true),
        ];
        $response = $this->post(route('api.settings.save'), $attributes);
        $attributes = ['key' => SettingsKey::APP_COPYRIGHT, 'value' => $value];

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
    public function a_user_can_upload_app_logo()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.upload', 'settings.branding']));
        $this->withPermissionsPolicy();

        $logo = 'testlogo.png';
        $attributes = [
            'test:logo' => UploadedFile::fake()->create('mypic.png'),
            'key' => 'test:logo', 'name' => 'testlogo',
        ];

        // Actions
        $response = $this->post(route('api.settings.upload'), $attributes);
        $attributes = ['key' => 'test:logo', 'value' => url($logo)];

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }
}
