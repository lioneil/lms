<?php

namespace Tests\Unit\Application\Sidebar;

use Core\Application\Sidebar\Sidebar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @package Tests\Unit\Application\Sidebar
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SidebarTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @test
     * @group  unit
     * @group  unit:app/sidebar
     * @return void
     */
    public function it_initializes_with_sidebar_manifest_as_first_parameter()
    {
        $sidebar = new Sidebar(
            $manifest = $this->app->make('manifest:sidebar'),
            $this->app->make('request')
        );

        $this->assertEquals($manifest, $sidebar->manifest());
    }
}
