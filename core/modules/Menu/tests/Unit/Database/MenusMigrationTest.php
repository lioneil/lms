<?php

namespace Menu\Unit\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Menu\Models\Menu;
use Tests\TestCase;

/**
 * @package Menu\Unit\Database
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class MenusMigrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->tablename = 'menus';
        $this->db = DB::getSchemaBuilder();
        $this->schema = Schema::getConnection()->getDoctrineSchemaManager();
        $this->columns = $this->schema->listTableColumns($this->tablename);
    }

    /**
     * @test
     * @group
     * @group  migration:menus
     * @return void
     */
    public function table_should_be_named_accordingly()
    {
        $this->assertEquals($this->tablename, with(new Menu)->getTable());
    }

    /**
     * @test
     * @group
     * @group  migration:menus
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
     * @group
     * @group  migration:menus
     * @return void
     */
    public function it_should_have_title_column()
    {
        $this->assertArrayHasKey('title', $this->columns);
        $this->assertTrue($this->columns['title']->getNotNull());
        $this->assertEquals('string', $this->db->getColumnType($this->tablename, 'title'));
    }

    /**
     * @test
     * @group
     * @group  migration:menus
     * @return void
     */
    public function it_should_have_uri_column()
    {
        $this->assertArrayHasKey('uri', $this->columns);
        $this->assertTrue($this->columns['uri']->getNotNull());
        $this->assertEquals('string', $this->db->getColumnType($this->tablename, 'uri'));
    }

    /**
     * @test
     * @group
     * @group  migration:menus
     * @return void
     */
    public function it_should_have_location_column()
    {
        $this->assertArrayHasKey('location', $this->columns);
        $this->assertTrue($this->columns['location']->getNotNull());
        $this->assertEquals('string', $this->db->getColumnType($this->tablename, 'location'));
    }

    /**
     * @test
     * @group
     * @group  migration:menus
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
     * @group
     * @group  migration:menus
     * @return void
     */
    public function it_should_have_sort_column()
    {
        $this->assertArrayHasKey('sort', $this->columns);
        $this->assertTrue($this->columns['sort']->getNotnull());
        $this->assertEquals('integer', $this->db->getColumnType($this->tablename, 'sort'));
    }

    /**
     * @test
     * @group
     * @group  migration:menus
     * @return void
     */
    public function it_should_have_parent_column()
    {
        $this->assertArrayHasKey('parent', $this->columns);
        $this->assertFalse($this->columns['parent']->getNotNull());
        $this->assertEquals('string', $this->db->getColumnType($this->tablename, 'parent'));
    }

    /**
     * @test
     * @group
     * @group  migration:menus
     * @return void
     */
    public function it_should_have_menuable_type()
    {
        $this->assertArrayHasKey('menuable_type', $this->columns);
        $this->assertTrue($this->columns['menuable_type']->getNotNull());
        $this->assertEquals('string', $this->db->getColumnType($this->tablename, 'menuable_type'));
    }

    /**
     * @test
     * @group
     * @group  migration:menus
     * @return void
     */
    public function it_should_have_menuable_id()
    {
        $this->assertArrayHasKey('menuable_id', $this->columns);
        $this->assertTrue($this->columns['menuable_id']->getNotNull());
        $this->assertEquals('integer', $this->db->getColumnType($this->tablename, 'menuable_id'));
    }

    /**
     * @test
     * @group
     * @group  migration:menus
     * @return void
     */
    public function it_should_have_lft_column()
    {
        $this->assertArrayHasKey('lft', $this->columns);
        $this->assertFalse($this->columns['lft']->getNotNull());
        $this->assertEquals('integer', $this->db->getColumnType($this->tablename, 'lft'));
    }

    /**
     * @test
     * @group
     * @group  migration:menus
     * @return void
     */
    public function it_should_have_rgt_column()
    {
        $this->assertArrayHasKey('rgt', $this->columns);
        $this->assertFalse($this->columns['rgt']->getNotNull());
        $this->assertEquals('integer', $this->db->getColumnType($this->tablename, 'rgt'));
    }

    /**
     * @test
     * @group
     * @group  migration:menus
     * @return void
     */
    public function it_should_have_created_at_column()
    {
        $this->assertArrayHasKey('created_at', $this->columns);
        $this->assertFalse($this->columns['created_at']->getNotNull());
        $this->assertEquals('datetime', $this->db->getColumnType($this->tablename, 'created_at'));
    }

    /**
     * @test
     * @group
     * @group  migration:menus
     * @return void
     */
    public function it_should_have_updated_at_column()
    {
        $this->assertArrayHasKey('updated_at', $this->columns);
        $this->assertFalse($this->columns['updated_at']->getNotNull());
        $this->assertEquals('datetime', $this->db->getColumnType($this->tablename, 'updated_at'));
    }
}
