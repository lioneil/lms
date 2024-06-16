<?php

namespace Tests\Unit\Manifests;

use Core\Manifests\ModuleManifest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * @package Tests
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ModuleManifestTest extends TestCase
{
    /** setup the module manifest instance */
    public function setUp(): void
    {
        parent::setUp();

        $this->manifest = $this->app->make(ModuleManifest::class);
        $this->files = $this->app->make('files');
        $this->manifestPath = base_path('bootstrap/cache/modules.php');
    }

    /** remove the files created from the tests */
    public function tearDown(): void
    {
        $this->files->delete($this->manifestPath);
        $this->manifest = null;
        $this->files = null;
        $this->manifestPath = null;

        parent::tearDown();
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:module
     * @return void
     */
    public function it_builds_the_manifest_file_and_writes_to_disk()
    {
        $this->manifest->build();

        $this->assertTrue($this->files->exists($this->manifestPath));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:module
     * @return void
     */
    public function it_can_retrieve_the_collection_of_enabled_modules()
    {
        $this->manifest->build();

        $this->assertInstanceOf(Collection::class, $this->manifest->modules());

        // Assume we know that a module called "dashboard"
        // is always present somewhere within the app
        // (and it does, otherwise the app will break).
        // The actual Dashboard module exists in
        // core/modules/Dashboard
        $this->assertTrue(array_key_exists(
            'Dashboard', $this->manifest->modules()->toArray()
        ));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:module
     * @return void
     */
    public function it_can_find_a_given_module_name()
    {
        // Write the manifest to disk.
        $this->manifest->build();

        // Assume we know that a module called "dashboard"
        // is always present somewhere within the app
        // (and it does, otherwise the app will break).
        // The actual Dashboard module exists in
        // core/modules/Dashboard
        $module = $this->manifest->find('dashboard');
        $this->assertInternalType('array', $module);
        $this->assertEquals('dashboard', strtolower($module['name']));

        // Repeat the test but for the alias method `module`.
        // Because why not.
        $module = $this->manifest->module('dashboard');
        $this->assertInternalType('array', $module);
        $this->assertEquals('dashboard', strtolower($module['name']));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:module
     * @return void
     */
    public function it_disables_a_module_from_the_manifest_file()
    {
        // Write the manifest to disk.
        $this->manifest->build();
        $modules = $this->manifest->modules();

        // Assume we know that a module called "dashboard"
        // is always present somewhere within the app
        // (and it does, otherwise the app will break).
        // The actual Dashboard module exists in
        // core/modules/Dashboard
        $this->manifest->remove('dashboard');
        $expected = $modules->count() - 1;
        $manifest = $this->files->getRequire($this->manifestPath);
        $actual = count($manifest['enabled']);

        $this->assertEquals($expected, $actual);
        $this->assertFalse(array_key_exists('Dashboard', $manifest['enabled']));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:module
     * @return void
     */
    public function it_can_retrieve_the_collection_of_disabled_modules()
    {
        // Write the manifest to disk.
        $this->manifest->build();
        // Remove the dashboard module.
        // This is our control scenario:
        // [[enabled] => [...],
        // [disabled] => [Dashboard]]
        // We know at least one value for
        // the `disabled` key.
        $this->manifest->remove('dashboard');

        // The disabled method should
        // return a collection of all disabled modules
        // from the manifest file.
        $modules = $this->manifest->disabled();
        $this->assertInstanceOf(Collection::class, $modules);

        // The Dashboard module should be listed
        // in the disabled array.
        $manifest = $this->files->getRequire($this->manifestPath);
        $this->assertTrue(in_array('Dashboard', $manifest['disabled']));
    }
}
