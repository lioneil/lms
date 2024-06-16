<?php

namespace Menu\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Menu\Models\Menu;
use Menu\Services\MenuServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Menu\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SaveMenusTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(MenuServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:menu
     * @return void
     */
    public function a_user_can_save_menu()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['menus.save']), ['menus.save']);
        $this->WithPermissionsPolicy();

        // Actions
        $attributes = ['menus' => factory(Menu::class, 3)->create()->toArray()];
        $response = $this->post(route('api.menus.save'), $attributes);

        // Assertions
        $response->assertSuccessful();
        collect($attributes['menus'])->each(function ($menu) {
            $this->assertDatabaseHas($this->service->getTable(), $menu);
        });
    }
}
