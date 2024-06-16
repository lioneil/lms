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
class ModuleControllerCommandTest extends TestCase
{
    /** setup the module manifest instance */
    public function setUp(): void
    {
        parent::setUp();

        $this->files = $this->app->make('files');
        $this->module = $this->app->make('manifest:module');
    }

    /** Remove all generated files */
    public function tearDown(): void
    {
        $path = $this->module('dashboard')->get('path');

        $this->files->delete(
            $path.'/Http/Controllers/TestController.php'
        );

        $this->files->delete(
            $path.'/Models/Test.php'
        );

        parent::tearDown();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:commands
     * @return void
     */
    public function it_will_generate_a_controller_file_to_a_specific_module()
    {
        $this->artisan('module:controller', [
            'name' => 'TestController',
            '--module' => 'Dashboard',
        ])->assertExitCode(0);

        $path = $this->module('dashboard')->get('path');
        $this->assertFileExists(
            $path.'/Http/Controllers/TestController.php'
        );
    }

    /**
     * @test
     * @group  feature
     * @group  feature:commands
     * @return void
     */
    public function it_will_prompt_to_pick_modules_when_no_module_option_is_supplied()
    {
        $this->artisan('module:controller', ['name' => 'TestController'])
             ->expectsQuestion('Pick the module the file will belong', 'Dashboard')
             ->assertExitCode(0);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:commands
     * @return void
     */
    public function it_will_prompt_and_generate_a_model_file_when_model_does_not_exist()
    {
        $this->artisan('module:controller', [
                'name' => 'TestController',
                '--module' => 'Dashboard',
                '--model' => 'Models/Test',
            ])
            ->expectsQuestion('A Dashboard\Models\Test model does not exist. Do you want to generate it?', true)
            ->expectsOutput('Model created successfully.')
            ->expectsOutput('Controller created successfully.')
            ->assertExitCode(0);

        $path = $this->module('dashboard')->get('path');
        $this->assertFileExists(
            $path.'/Models/Test.php',
            'Failed asserting that the model Test exists on the module Dashboard'
        );
    }

    /**
     * @test
     * @group  feature
     * @group  feature:commands
     * @return void
     */
    public function it_will_generate_a_resource_controller()
    {
        $this->artisan('module:controller', [
                'name' => 'TestController',
                '--module' => 'Dashboard',
                '--resource' => true,
            ])
            ->expectsOutput('Controller created successfully.');

        $path = $this->module('dashboard')->get('path');
        $this->assertFileExists(
            $path.'/Http/Controllers/TestController.php'
        );
    }

    /**
     * Retrieve the module given a name.
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function module($name)
    {
        return collect($this->module->find($name));
    }
}
