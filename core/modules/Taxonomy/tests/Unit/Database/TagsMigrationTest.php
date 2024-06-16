<?php

namespace Taxonomy\Unit\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Taxonomy\Models\Tag;
use Tests\TestCase;

/**
 * @package Taxonomy\Unit\Database
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TagsMigrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->tablename = 'tags';
        $this->db = DB::getSchemaBuilder();
        $this->schema = Schema::getConnection()->getDoctrineSchemaManager();
        $this->columns = $this->schema->listTableColumns($this->tablename);
    }

    /**
     * @test
     * @group  unit
     * @group  migration:tags
     * @return void
     */
    public function table_should_be_named_accordingly()
    {
        $this->assertEquals($this->tablename, with(new Tag)->getTable());
    }

    /**
     * @test
     * @group  unit
     * @group  migration:courses
     * @return void
     */
    public function it_should_have_id_incrementing_column()
    {
        $this->assertArrayHasKey('id', $this->columns);
        $this->assertTrue($this->columns['id']->getAutoincrement());
        $this->assertEquals('integer', $this->db->getColumnType($this->tablename, 'id'));
    }

    /**
     * @test
     * @group  unit
     * @group  migration:courses
     * @return void
     */
    public function it_should_have_name_column()
    {
        $this->assertArrayHasKey('name', $this->columns);
        $this->assertTrue($this->columns['name']->getNotNull());
        $this->assertEquals('string', $this->db->getColumnType($this->tablename, 'name'));
    }

    /**
     * @test
     * @group  unit
     * @group  migration:courses
     * @return void
     */
    public function it_should_have_icon_column()
    {
        $this->assertArrayHasKey('icon', $this->columns);
        $this->assertFalse($this->columns['icon']->getNotNull());
        $this->assertEquals('string', $this->db->getColumnType($this->tablename, 'icon'));
    }

    /**
     * @test
     * @group  unit
     * @group  migration:courses
     * @return void
     */
    public function it_should_have_type_column()
    {
        $this->assertArrayHasKey('type', $this->columns);
        $this->assertFalse($this->columns['type']->getNotNull());
        $this->assertEquals('string', $this->db->getColumnType($this->tablename, 'type'));
    }
}
