<?php

namespace Tests\Unit\Manifests;

use Core\Manifests\ThemeManifest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * @package Tests\Unit\Manifests
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ThemeManifestTest extends TestCase
{
    /** setup the theme manifest instance */
    public function setUp(): void
    {
        parent::setUp();

        $this->manifest = $this->app->make(ThemeManifest::class);
        $this->files = $this->app->make('files');
        $this->manifestPath = base_path('bootstrap/cache/themes.php');
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
     * @group  manifest:theme
     * @return void
     */
    public function it_builds_the_theme_manifest_file_and_writes_to_disk()
    {
        $this->manifest->build();

        $this->assertTrue($this->files->exists($this->manifestPath));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:theme
     * @return void
     */
    public function it_can_retrieve_the_collection_of_enabled_themes()
    {
        $this->manifest->build();

        $this->assertInstanceOf(Collection::class, $this->manifest->themes());

        // Assume we know that a theme called "default"
        // is always present somewhere within the app
        // (and it does, otherwise the app will
        // not render any views).
        // The actual default theme exists in
        // core/theme
        $this->assertTrue(array_key_exists(
            'Default', $this->manifest->themes()->toArray()
        ));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:theme
     * @return void
     */
    public function it_can_find_a_given_theme_name()
    {
        // Write the manifest to disk.
        $this->manifest->build();

        // Assume we know that a theme called "default"
        // is always present somewhere within the app
        // (and it does, otherwise the app will
        // not render any views).
        // The actual default theme exists in
        // core/theme
        $theme = $this->manifest->find('default');
        $this->assertInternalType('array', $theme);
        $this->assertEquals('default', strtolower($theme['name']));

        // Repeat the test but for the alias method `theme`.
        // Because why not.
        $theme = $this->manifest->theme('default');
        $this->assertInternalType('array', $theme);
        $this->assertEquals('default', strtolower($theme['name']));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:theme
     * @return void
     */
    public function it_disables_a_theme_from_the_manifest_file()
    {
        // Write the manifest to disk.
        $this->manifest->build();
        $themes = $this->manifest->themes();

        // Assume we know that a theme called "default"
        // is always present somewhere within the app
        // (and it does, otherwise the app will
        // not render any views).
        // The actual default theme exists in
        // core/theme
        $this->manifest->remove('default');
        $expected = $themes->count() - 1;
        $manifest = $this->files->getRequire($this->manifestPath);
        $actual = count($manifest['enabled']);

        $this->assertEquals($expected, $actual);
        $this->assertFalse(array_key_exists('Default', $manifest['enabled']));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:theme
     * @return void
     */
    public function it_can_retrieve_the_collection_of_disabled_themes()
    {
        // Write the manifest to disk.
        $this->manifest->build();
        // Remove the default theme.
        // This is our control scenario:
        // [[enabled] => [...],
        // [disabled] => [Default]]
        // We know at least one value for
        // the `disabled` key.
        $this->manifest->remove('default');

        // The disabled method should
        // return a collection of all disabled themes
        // from the manifest file.
        $themes = $this->manifest->disabled();
        $this->assertInstanceOf(Collection::class, $themes);

        // The Default theme should be listed
        // in the disabled array.
        $manifest = $this->files->getRequire($this->manifestPath);
        $this->assertTrue(in_array('Default', $manifest['disabled']));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:theme
     * @return void
     */
    public function it_can_activate_a_theme()
    {
        // Write the manifest to disk.
        $this->manifest->build();

        // Activate a known theme 'default',
        $this->manifest->activate('default');

        $expected = $this->manifest->find('default');
        $actual = $this->files->getRequire($this->manifestPath)['active'];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:manifests
     * @group  manifest:theme
     * @return void
     */
    public function it_can_retrieve_the_active_theme()
    {
        // Write the manifest to disk.
        $this->manifest->build();

        // Activate a known theme 'default',
        $this->manifest->activate('default');

        // It should retrieve the set active theme.
        $expected = $this->manifest->active();
        $this->assertInstanceOf(Collection::class, $expected);

        $actual = $this->files->getRequire($this->manifestPath)['active'];
        $this->assertEquals('Default', $actual['name']);
    }
}
