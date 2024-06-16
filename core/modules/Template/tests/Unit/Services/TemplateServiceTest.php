<?php

namespace Template\Unit\Services;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Template\Models\Template;
use Template\Services\TemplateServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Template\Unit\Services
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TemplateServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(TemplateServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:template
     * @return void
     */
    public function it_can_return_a_paginated_list_of_templates()
    {
        // Arrangements
        $templates = factory(Template::class, 5)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:template
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_templates()
    {
        // Arrangements
        $templates = factory(Template::class, 5)->create();
        $templates->each->delete();

        // Actions
        $actual = $this->service->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }
}
