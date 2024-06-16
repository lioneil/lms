<?php

namespace Tests\Unit\Application\Breadcrumbs;

use Core\Application\Breadcrumbs\Breadcrumbs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @package Tests\Unit\Application\Breadcrumbs
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class BreadcrumbsTest extends TestCase
{
    /**
     * @test
     * @group  unit
     * @group  unit:breadcrumbs
     * @return void
     */
    public function it_can_return_the_segments()
    {
        // Assembly
        $url = 'admin/users/1';
        $this->get($url);
        $breadcrumbs = new Breadcrumbs($request = $this->app['request']);

        // Action
        $expected = explode('/', $url);
        $actual = $breadcrumbs->all();

        // Assertion
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:breadcrumbs
     * @return void
     */
    public function it_can_return_the_request_object()
    {
        // Assembly
        $url = 'admin/users/1';
        $this->get($url);
        $breadcrumbs = new Breadcrumbs($request = $this->app['request']);

        // Action
        $actual = $breadcrumbs->request();

        // Assertion
        $this->assertInstanceOf(\Illuminate\Http\Request::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:breadcrumbs
     * @return void
     */
    public function it_can_retrieve_a_segment()
    {
        // Assembly
        $url = 'admin/users/1';
        $this->get($url);
        $breadcrumbs = new Breadcrumbs($request = $this->app['request']);

        // Action
        $expected = ['text' => 'Users', 'url' => 'admin/users'];
        $actual = $breadcrumbs->segment(1);

        // Assertion
        $this->assertEquals($expected, $actual);
    }
}
