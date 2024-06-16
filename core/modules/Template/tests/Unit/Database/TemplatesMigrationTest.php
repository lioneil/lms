<?php

namespace Template\Unit\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Template\Models\Template;
use Tests\TestCase;

/**
 * @package Template\Unit\Database
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TemplatesMigrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The table name of
     * the migration to be tested.
     *
     * @var string
     */
    protected $tablename = 'templates';

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->db = DB::getSchemaBuilder();
        $this->schema = Schema::getConnection()->getDoctrineSchemaManager();
        $this->columns = $this->schema->listTableColumns($this->tablename);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:migration
     * @group  unit:migration:templates
     * @return void
     */
    public function table_should_be_named_accordingly()
    {
        $this->assertEquals($this->tablename, with(new Template)->getTable());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:migration
     * @group  unit:migration:templates
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
     * @group  unit:migration
     * @group  unit:migration:templates
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
     * @group  unit:migration
     * @group  unit:migration:templates
     * @return void
     */
    public function it_should_have_code_column()
    {
        $this->assertArrayHasKey('code', $this->columns);
        $this->assertTrue($this->columns['code']->getNotNull());
        $this->assertEquals('string', $this->db->getColumnType($this->tablename, 'code'));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:migration
     * @group  unit:migration:templates
     * @return void
     */
    public function it_should_have_description_column()
    {
        $this->assertArrayHasKey('description', $this->columns);
        $this->assertFalse($this->columns['description']->getNotNull());
        $this->assertEquals('text', $this->db->getColumnType($this->tablename, 'description'));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:migration
     * @group  unit:migration:templates
     * @return void
     */
    public function it_should_have_pathname_column()
    {
        $this->assertArrayHasKey('pathname', $this->columns);
        $this->assertTrue($this->columns['pathname']->getNotNull());
        $this->assertEquals('text', $this->db->getColumnType($this->tablename, 'pathname'));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:migration
     * @group  unit:migration:templates
     * @return void
     */
    public function it_should_have_type_column()
    {
        $this->assertArrayHasKey('type', $this->columns);
        $this->assertTrue($this->columns['type']->getNotNull());
        $this->assertEquals('string', $this->db->getColumnType($this->tablename, 'type'));
        $this->assertEquals('template', $this->columns['type']->getDefault());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:migration
     * @group  unit:migration:templates
     * @return void
     */
    public function it_should_have_metadata_column()
    {
        $this->assertArrayHasKey('metadata', $this->columns);
        $this->assertFalse($this->columns['metadata']->getNotNull());
        $this->assertEquals('text', $this->db->getColumnType($this->tablename, 'metadata'));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:migration
     * @group  unit:migration:templates
     * @return void
     */
    public function it_should_have_user_id_column()
    {
        $this->assertArrayHasKey('user_id', $this->columns);
        $this->assertTrue($this->columns['user_id']->getNotNull());
        $this->assertEquals('integer', $this->db->getColumnType($this->tablename, 'user_id'));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:migration
     * @group  unit:migration:templates
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
     * @group  unit
     * @group  unit:migration
     * @group  unit:migration:templates
     * @return void
     */
    public function it_should_have_updated_at_column()
    {
        $this->assertArrayHasKey('updated_at', $this->columns);
        $this->assertFalse($this->columns['updated_at']->getNotNull());
        $this->assertEquals('datetime', $this->db->getColumnType($this->tablename, 'updated_at'));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:migration
     * @group  unit:migration:templates
     * @return void
     */
    public function it_should_have_deleted_at_column()
    {
        $this->assertArrayHasKey('deleted_at', $this->columns);
        $this->assertFalse($this->columns['deleted_at']->getNotNull());
        $this->assertEquals('datetime', $this->db->getColumnType($this->tablename, 'deleted_at'));
    }
}
