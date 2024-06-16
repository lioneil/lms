<?php

namespace Test\Widget\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use Widget\Models\Widget;
use Widget\Services\WidgetServiceInterface;
use Widget\Services\WidgetService;

/**
 * @package Widget\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class WidgetsTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker ,ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(WidgetServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * Add. test pass
     *
     * @test
     * @group  feature
     * @group  feature:widget
     * @group  widgets.index
     * @return void
     */
    public function a_user_can_view_list_of_all_widgets()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $widgets = factory(Widget::class, 5)->create();

        // Actions
        $response = $this->get(route('widgets.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('widget::settings.index')
                 ->assertSeeText('name')
                 ->assertSeeText('description');
                 //->assertSeeTextInOrder($widgets->pluck('file')->toArray());
    }

    /**
     * Add. Failed asserting that a row in the table [coursewares] matches the attributes {
     *
     * @test
     * @group  feature
     * @group  feature:widget
     * @group  widgets.store
     * @return void
     */
    public function a_super_user_can_add_a_widget_to_database()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());

        // Actions
        $attributes = factory(Widget::class)->make(['id' => $user->getKey()])->toArray();
        $response = $this->post(route('widgets.store'), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $response->assertRedirect(route('widgets.index'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:widget
     * @group  widgets.show
     * @return void
     */
    public function a_super_user_can_visit_a_widget()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $widget = factory(Widget::class, 4)->create()->random();

        // Actions
        $response = $this->get(route('widgets.show', $widget->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resource')
                 ->assertViewIs('widget::admin.show');
    }

    /**
     * Read Test pass
     *
     * @test
     * @group  feature
     * @group  feature:widget
     * @group  widgets.refresh
     * @return void
     */
    public function ability_to_refresh_widget()
    {
        $this->actingAs($user = $this->superAdmin);
        $widgets = factory(Widget::class, 5)->create();

        // Actions
        $response = $this->get(route('widgets.refresh'));

        // Assertions
        $response->assertSuccessful();
                 //->assertViewHas('resources');
                 //->assertViewIs('widget::settings.index');
    }
}
