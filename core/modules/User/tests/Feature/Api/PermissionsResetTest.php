<?php

namespace User\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Services\PermissionServiceInterface;

/**
 * @package User\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PermissionsResetTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(PermissionServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:permission
     * @return void
     */
    public function a_user_can_refresh_the_permissions_list()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['permissions.refresh']), ['permissions.refresh']);
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->post(route('api.permissions.refresh'));

        // Assertions
        $response->assertSuccessful();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:permission
     * @return void
     */
    public function a_user_can_reset_the_permissions_list()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['permissions.reset']), ['permissions.reset']);
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->post(route('api.permissions.reset'));

        // Assertions
        $response->assertSuccessful();
    }
}
