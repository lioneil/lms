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
class SettingsSystemTest extends TestCase
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
    public function a_user_can_retrieve_app_version()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_VERSION => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:version'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_environment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_ENVIRONMENT => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:environment'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_author()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_AUTHOR => $expected = $this->faker->name()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:author'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_year()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_YEAR => $expected = $this->faker->year()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:year'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_timezone()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_TIMEZONE => $expected = $this->faker->timezone()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:timezone'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_debug()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_DEBUG => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:debug'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_module()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_MODULE => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:module'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_themes()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_THEME => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:theme'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_connection()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_CONNECTION => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:connection'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_host()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_HOST => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:host'));
    }


    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_port()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_PORT => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:port'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_DATABASE => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:database'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_username()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_USERNAME => $expected = $this->faker->name()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:username'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_mail_environment()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_ENVIRONMENT => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:environment'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_mail_host()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_MAIL_HOST => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:mailhost'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_mail_port()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_MAIL_PORT => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:mailport'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_encryption()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_ENCRYPTION => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:encryption'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_server_software()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_SERVER_SOFTWARE => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:serversoftware'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_server_admin()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_SERVER_ADMIN => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:serveradmin'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_document_root()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_DOCUMENT_ROOT => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:documentroot'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_remote_address()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_REMOTE_ADDRESS => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:remoteaddress'));
    }

     /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_php_version()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_PHP_VERSION => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:phpversion'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_maximum_file_uploads()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_MAXIMUM_FILE_UPLOADS => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:maxfileuploads'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_maximum_size()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_MAXIMUM_SIZE => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:maximumsize'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_max_file_size()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_MAX_FILE_SIZE => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:maxfilesize'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_max_file_os()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_OS => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:os'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_release_name()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_RELEASE_NAME => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:releasename'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:setting
     * @group  feature:setting:api
     * @return void
     */
    public function a_user_can_retrieve_app_user_agent()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['settings.index']));
        $this->withPermissionsPolicy();

        settings([SettingsKey::APP_USER_AGENT => $expected = $this->faker->word()])->save();

        // Actions
        $attributes = ['key' => 'app'];
        $response = $this->get(route('api.settings.index', $attributes));
        $actual = $response->original;

        // Assertions
        $response->assertSuccessful();
        $this->assertSame($expected, $actual->get('app:useragent'));
    }
}
