<?php

namespace Setting\Unit\Services;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Setting\Enumerations\SettingsKey;
use Setting\Services\SettingServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Setting\Unit\Services
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SettingsServiceTest extends TestCase
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
     * @group  unit
     * @group  unit:service
     * @group  unit:service:settings
     * @return void
     */
    public function it_can_save_multiple_key_value_pairs_to_settings_table()
    {
        // Arrangements
        $attributes = [
            SettingsKey::APP_TITLE => $title = $this->faker->words($nb = 3, $asText = true),
            SettingsKey::APP_TAGLINE => $tagline = $this->faker->words($nb = 9, $asText = true),
            SettingsKey::APP_DEV => $dev = $this->faker->name(),
        ];

        // Actions
        $this->service->store($attributes);
        $attributes = [
            ['key' => SettingsKey::APP_TITLE, 'value' => $title],
            ['key' => SettingsKey::APP_TAGLINE, 'value' => $tagline],
            ['key' => SettingsKey::APP_DEV, 'value' => $dev],
        ];

        // Assertions
        collect($attributes)->each(function ($item) {
            $this->assertDatabaseHas($this->service->getTable(), $item);
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:settings
     * @return void
     */
    public function it_can_upload_logo_to_settings_table()
    {
        // Arrangements
        $logo = 'testlogo.png';
        $attributes = [SettingsKey::APP_LOGO => UploadedFile::fake()->create('mypic.png')];

        // Actions
        $this->service->upload($attributes, SettingsKey::APP_LOGO, 'testlogo');
        $attributes = ['key' => SettingsKey::APP_LOGO, 'value' => url($logo)];

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }
}
