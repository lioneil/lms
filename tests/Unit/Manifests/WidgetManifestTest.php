<?php

namespace Tests\Unit\Manifests;

use Core\Manifests\WidgetManifest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * @package Tests\Unit\Manifests
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class WidgetManifestTest extends TestCase
{
    /** setup the theme manifest instance */
    public function setUp(): void
    {
        parent::setUp();

        $this->manifest = $this->app->make(WidgetManifest::class);
        $this->files = $this->app->make('files');
        $this->manifestPath = base_path('bootstrap/cache/widgets.php');
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
     * @group  manifest:widget
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
     * @group  manifest:widget
     * @return void
     */
    public function it_can_retrieve_the_collection_of_enabled_widgets()
    {
        $this->manifest->build();

        $this->assertInstanceOf(Collection::class, $this->manifest->enabled());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:widget
     * @return void
     */
    public function it_can_find_a_given_theme_name()
    {
        // Write the manifest to disk.
        $this->manifest->build();

        // Assume we know that a widget called "user:count"
        // is always present somewhere within the app
        // The actual widget can be found in
        // User/Widgets/UserCount
        $widget = $this->manifest->find('user:count');
        $this->assertInternalType('array', $widget);
        $this->assertEquals('user:count', strtolower($widget['alias']));

        // Repeat the test but for the alias method `widget`.
        // Because why not.
        $widget = $this->manifest->widget('user:count');
        $this->assertInternalType('array', $widget);
        $this->assertEquals('user:count', strtolower($widget['alias']));
    }
}
