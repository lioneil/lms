<?php

namespace Setting\Feature\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Setting\Enumerations\SettingsKey;
use Setting\Models\Setting;
use Setting\Services\SettingServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Setting\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SettingsTest extends TestCase
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
    public function a_user_can_retrieve_list_of_settings()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_TITLE => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:title'));
    }
}
