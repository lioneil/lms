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
class SettingsCommentingTest extends TestCase
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
    public function a_user_can_enable_the_commenting_option()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.save']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = [
            SettingsKey::COMMENTING_ENABLE => $value = $this->faker->boolean(),
        ];
        $response = $this->post(route('api.settings.save'), $attributes);
        $attributes = ['key' => SettingsKey::COMMENTING_ENABLE, 'value' => $value];

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
    public function a_user_can_add_words_to_the_blacklist()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.save']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = [
            SettingsKey::BLACKLISTED_WORDS => $value = implode(',', $this->faker->words()),
        ];
        $response = $this->post(route('api.settings.save'), $attributes);
        $attributes = ['key' => SettingsKey::BLACKLISTED_WORDS, 'value' => $value];

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
    public function a_user_can_toggle_check_for_exact_word_match()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.save']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = [
            SettingsKey::BLACKLISTED_EXACT => $value = $this->faker->boolean(),
        ];
        $response = $this->post(route('api.settings.save'), $attributes);
        $attributes = ['key' => SettingsKey::BLACKLISTED_EXACT, 'value' => $value];

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }
}
