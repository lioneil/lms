<?php

namespace Tests\Course\Unit\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * @package Course\Unit\Database
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PublishableFieldsCoursesMigrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->tablename = 'courses';
        $this->db = DB::getSchemaBuilder();
        $this->schema = Schema::getConnection()->getDoctrineSchemaManager();
        $this->columns = $this->schema->listTableColumns($this->tablename);
    }

    /**
     * @test
     * @group  unit
     * @group  migration:courses
     * @return void
     */
    public function it_should_have_published_at_column()
    {
        $this->assertArrayHasKey('published_at', $this->columns);
        $this->assertFalse($this->columns['published_at']->getNotNull());
        $this->assertEquals('datetime', $this->db->getColumnType($this->tablename, 'published_at'));
    }

    /**
     * @test
     * @group  unit
     * @group  migration:courses
     * @return void
     */
    public function it_should_have_drafted_at_column()
    {
        $this->assertArrayHasKey('drafted_at', $this->columns);
        $this->assertFalse($this->columns['drafted_at']->getNotNull());
        $this->assertEquals('datetime', $this->db->getColumnType($this->tablename, 'drafted_at'));
    }

    /**
     * @test
     * @group  unit
     * @group  migration:courses
     * @return void
     */
    public function it_should_have_expired_at_column()
    {
        $this->assertArrayHasKey('expired_at', $this->columns);
        $this->assertFalse($this->columns['expired_at']->getNotNull());
        $this->assertEquals('datetime', $this->db->getColumnType($this->tablename, 'expired_at'));
    }
}
