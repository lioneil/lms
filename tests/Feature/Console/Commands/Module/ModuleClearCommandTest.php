<?php

namespace Tests\Feature\Console\Commands\Module;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @package Tests\Feature\Console\Commands\Module
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ModuleClearCommandTest extends TestCase
{
    /** Rebuild the cache file when the tests have been run */
    public function tearDown(): void
    {
        $this->artisan('module:discover');

        parent::tearDown();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:commands
     * @return void
     */
    public function it_can_delete_the_module_manifest_file()
    {
        $this->artisan('module:clear')
             ->expectsOutput('Module manifest cache cleared!')
             ->assertExitCode(0);

        $this->assertTrue(! file_exists(base_path('bootstrap/cache/modules.php')));
    }
}
