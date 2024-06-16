<?php

namespace Tests\Unit\Manifests;

use Core\Manifests\SidebarManifest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * @package Tests\Unit\Manifests
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SidebarManifestTest extends TestCase
{
    /** setup the sidebar manifest instance */
    public function setUp(): void
    {
        parent::setUp();

        $this->manifest = $this->app->make(SidebarManifest::class);
        $this->files = $this->app->make('files');
        $this->manifestPath = base_path('bootstrap/cache/sidebar.php');
    }

    /** remove the files created from the tests */
    public function tearDown(): void
    {
        $this->manifest = null;
        $this->files = null;
        $this->manifestPath = null;

        parent::tearDown();
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:sidebar
     * @return void
     */
    public function it_can_build_the_sidebar_cache_and_write_to_disk()
    {
        $this->manifest->build();

        $this->assertTrue($this->files->exists($this->manifestPath));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:sidebar
     * @return void
     */
    public function it_can_retrieve_the_collection_of_enabled_sidebar()
    {
        $this->manifest->build();

        $this->assertInstanceOf(Collection::class, $this->manifest->all());

        // Assume we know that a menu from the "dashboard" module
        // is always present in Dashboard/config/sidebar.php,
        // It should return as a top level menu.
        $this->assertTrue(array_key_exists(
            'dashboard', $this->manifest->all()->toArray()
        ));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:sidebar
     * @return void
     */
    public function it_can_find_a_given_sidebar_name()
    {
        // Write the manifest to disk.
        $this->manifest->build();

        // Assume we know that a menu from the "dashboard" module
        // is always present in Dashboard/config/sidebar.php,
        // It should return as a top level menu.
        $sidebar = $this->manifest->find('dashboard');
        $this->assertInternalType('array', $sidebar);
        $this->assertEquals('dashboard', strtolower($sidebar['name']));

        // Repeat the test but for the alias method `sidebar`.
        // Because why not.
        $sidebar = $this->manifest->sidebar('dashboard');
        $this->assertInternalType('array', $sidebar);
        $this->assertEquals('dashboard', strtolower($sidebar['name']));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:sidebar
     * @return void
     */
    public function it_can_delete_the_generated_sidebar_manifest()
    {
        // Write the manifest to disk.
        $this->manifest->build();

        $this->manifest->destroy();

        $this->assertTrue(! $this->files->exists($this->manifestPath));
    }
}
