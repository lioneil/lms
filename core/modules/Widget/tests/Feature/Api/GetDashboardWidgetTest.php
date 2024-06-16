<?php

namespace Widget\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\Role;
use Widget\Models\Widget;
use Widget\Services\WidgetServiceInterface;

/**
 * @package Widget\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class GetDashboardWidgetTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(WidgetServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:widget:list
     * @return void
     */
    public function a_user_can_retrieve_list_of_dashboard_widgets_available_on_their_role()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['widgets.list']), ['widgets.list']);
        $this->withPermissionsPolicy();

        $widget = factory(Widget::class)->create();
        $widgets = factory(Widget::class, 2)->create();
        $roles = $user->roles->pluck('id')->toArray();
        $widgets->each(function ($widget) use ($roles) {
            $widget->roles()->sync($roles);
        });

        // Actions
        $attributes = ['roles' => $roles];
        $response = $this->get(route('api.widgets.list', $attributes));

        // Assertions
        $response->assertSuccessful()
                 ->assertJsonStructure([
                    'data' => [[
                        'file', 'namespace', 'name',
                        'roles', 'created', 'modified',
                    ]],
                ]);
        $response->original->each(function ($widget) use ($roles) {
            $this->assertSame($roles, $widget->roles->pluck('id')->toArray());
        });
    }
}
