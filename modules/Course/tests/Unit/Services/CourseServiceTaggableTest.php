<?php

namespace Course\Unit\Services;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Taxonomy\Models\Tag;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Unit\Services
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CourseServiceTaggableTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CourseServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_can_return_a_collection_of_tags()
    {
        // Arrangements
        $tags = factory(Tag::class, 2)->make();

        // Actions
        $tags = $this->service->getOrSaveTags($tags->toArray());

        // Assertions
        $this->assertInstanceOf(Collection::class, $tags);
    }
}
