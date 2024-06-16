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
class WidgetsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(WidgetServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:widget
     * @return void
     */
    public function a_user_can_only_view_their_owned_widgets()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['widgets.index']), ['widgets.index']);
        $this->withPermissionsPolicy();

        $widgets = factory(Widget::class, 2)->create();

        // Actions
        $response = $this->get(route('api.widgets.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJsonStructure([
                    'data' => [[
                        'file', 'namespace', 'fullname',
                        'name', 'alias', 'description',
                        'created', 'modified',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:widget
     * @return void
     */
    public function a_user_can_visit_widget_page()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['widgets.show']), ['widgets.show']);
        $this->withPermissionsPolicy();

        $widget = factory(Widget::class, 5)->create()->random();

        // Actions
        $response = $this->get(route('api.widgets.show', $widget->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJsonStructure([
                    'data' => [
                        'file', 'namespace', 'fullname',
                        'name', 'alias', 'description',
                        'created', 'modified',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:widget
     * @return void
     */
    public function a_user_can_store_a_widget_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['widgets.store']), ['widgets.store']);
        $this->withPermissionsPolicy();

        $widget = factory(Widget::class)->create();
        $widget->roles()->sync($user->roles->pluck('id'));

        // Actions
        $widget = factory(Widget::class)->make();
        $attributes = array_merge($widget->toArray(), [
            'roles' => $roles = $user->roles()->pluck('id')->toArray(),
        ]);
        $response = $this->post(route('api.widgets.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $response->original->each(function ($widget) use ($roles) {
            $this->assertSame($roles, $widget->roles->pluck('id')->toArray());
        });
        $this->assertDatabaseHas($this->service->getTable(), $widget->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:widget
     * @return void
     */
    public function a_user_can_update_a_widget()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['widgets.update']), ['widgets.update']);
        $this->withPermissionsPolicy();
        $original = factory(Widget::class, 2)->create()->random();

        // Actions
        $widget = factory(Widget::class)->make();
        $attributes = array_merge($widget->toArray(), [
            'roles' => $user->roles->pluck('id')->first(),
        ]);
        $response = $this->put(route('api.widgets.update', $original->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($widget->getTable(), $widget->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:widget
     * @return void
     */
    public function a_user_can_refresh_the_widget_list()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['widgets.refresh']), ['widgets.refresh']);
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->post(route('api.widgets.refresh'));

        // Assertions
        $response->assertSuccessful();
    }
}
