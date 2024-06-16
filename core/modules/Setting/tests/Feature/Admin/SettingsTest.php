<?php

namespace Tests\Setting\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Setting\Models\Setting;
use Setting\Services\SettingServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Setting\Feature\Admin
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
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  settings.preference
     * @return void
     */
    public function a_user_can_save_preferred_date_format()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['settings.store', 'settings.preferences']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = ['format:date' => ':human:'];

        $response = $this->post(
            route('settings.store'), $attributes,
            ['HTTP_REFERER' => route('settings.preferences')]
        );

        // Assertions
        $response->assertRedirect(route('settings.preferences'));
        $this->assertDatabaseHas($this->service->getTable(), [
            'key' => 'format:date', 'value' => $attributes['format:date']
        ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  settings.preference
     * @return void
     */
    public function a_user_can_save_preferred_time_format()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['settings.store', 'settings.preferences']));
        $this->withPermissionsPolicy();

        // Actions
        $attributes = ['format:time' => ':human:'];

        $response = $this->post(
            route('settings.store'), $attributes,
            ['HTTP_REFERER' => route('settings.preferences')]
        );

        // Assertions
        $response->assertRedirect(route('settings.preferences'));
        $this->assertDatabaseHas($this->service->getTable(), [
            'key' => 'format:time',
            'value' => $attributes['format:time']
        ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  settings.update
     * @return void
     */
    public function a_user_can_edit_date_format()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['settings.store', 'settings.preferences']));
        $this->withPermissionsPolicy();

        // Actions
        settings(['format:date' => 'm/d/Y'])->save();

        $attributes = ['format:date' => ':human:'];

        $response = $this->post(
            route('settings.store', $attributes));

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), [
            'key' => 'format:date',
            'value' => $attributes['format:date']
        ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  settings.update
     * @return void
     */
    public function a_user_can_edit_time_format()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['settings.store', 'settings.preferences']));
        $this->withPermissionsPolicy();

        // Actions
        settings(['format:time' => 'H:i:s'])->save();

        $attributes = ['format:time' => ':human:'];

        $response = $this->post(
            route('settings.store', $attributes));

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), [
            'key' => 'format:time',
            'value' => $attributes['format:time']
        ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  settings.update
     * @return void
     */
    public function a_user_can_edit_date_time_format()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['settings.store', 'settings.preferences']));
        $this->withPermissionsPolicy();

        // Actions
        settings(['format:datetime' => 'Y/m/d H:i:s'])->save();

        $attributes = ['format:datetime' => ':human:', 'format:datetime'=>'Y/m/d H:i:s'];

        $response = $this->post(route('settings.store', $attributes));

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), [
            'key' => 'format:datetime',
            'value' => $attributes['format:datetime']
        ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  settings.update
     * @return void
     */
    public function a_user_can_edit_app_title()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['settings.store', 'settings.branding']));
        $this->withPermissionsPolicy();

        // Actions
        settings(['app:title' => 'New App Title'])->save();

        $attributes = ['app:title' => 'New App Title'];

        $response = $this->post(
            route('settings.store', $attributes));

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), [
            'key' => 'app:title',
            'value' => $attributes['app:title']
        ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  settings.update
     * @return void
     */
    public function a_user_can_edit_app_subtitle()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['settings.store', 'settings.branding']));
        $this->withPermissionsPolicy();

        // Actions
        settings(['app:subtitle' => 'New App Subtitle'])->save();

        $attributes = ['app:subtitle' => 'New App Subtitle'];

        $response = $this->post(route('settings.store', $attributes));

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), [
            'key' => 'app:subtitle',
            'value' => $attributes['app:subtitle']
        ]);
    }

     /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  settings.update
     * @return void
     */
    public function a_user_can_edit_app_author()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['settings.store', 'settings.branding']));
        $this->withPermissionsPolicy();

        // Actions
        settings(['app:author' => 'New App Author'])->save();

        $attributes = ['app:author' => 'New App Author'];

        $response = $this->post(
            route('settings.store', $attributes));

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), [
            'key' => 'app:author',
            'value' => $attributes['app:author']
        ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  settings.update
     * @return void
     */
    public function a_user_can_edit_app_year()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['settings.store', 'settings.branding']));
        $this->withPermissionsPolicy();

        // Actions
        settings(['app:year' => 'New App Year'])->save();

        $attributes = ['app:year' => 'New App Year'];

        $response = $this->post(
            route('settings.store', $attributes));

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), [
            'key' => 'app:year',
            'value' => $attributes['app:year']
        ]);
    }

     /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  settings.update
     * @return void
     */
    public function a_user_can_edit_app_email()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['settings.store', 'settings.branding']));
        $this->withPermissionsPolicy();

        // Actions
        settings(['app:email' => 'New App Year'])->save();

        $attributes = ['app:email' => 'New App Year'];

        $response = $this->post(
            route('settings.store', $attributes));

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), ['key'=>'app:email','value'=>$attributes['app:email']]);
    }

     /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  settings.update
     * @return void
     */
    public function a_user_can_upload_image()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['settings.store', 'settings.branding']));
        $this->withPermissionsPolicy();

        Storage::fake('avatars');

        $response = $this->post(
            route('settings.store', UploadedFile::fake()->image('avatar.jpg')));

        // Assert the file was stored...
        Storage::disk('avatars')->assertExists('avatar.jpg');
    }
}
