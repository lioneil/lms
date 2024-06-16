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
class GetMenuLocationsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(MenuServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:menu:location
     * @return void
     */
    public function a_user_can_retrieve_list_of_menus_available_on_their_location()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['menus.locations']), ['menus.locations']);
        $this->withPermissionsPolicy();

        $menu = factory(Menu::class, 3)->create()->random();

        // Actions
        $response = $this->get(route('api.menus.locations', $menu->getDefaultLocation()));

        // Assertions
        $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [[
                'title', 'uri', 'location',
                'icon', 'sort', 'parent',
                'menuable_id', 'menuable_type', 'lft',
                'rgt',
            ]],
        ]);
    }
}
