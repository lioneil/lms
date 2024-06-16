<?php

namespace Tests\Course\Unit\Database;

use Course\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Taxonomy\Models\Category;
use Tests\TestCase;
use User\Models\User;

/**
 * @package Course\Unit\Database
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CoursesMigrationTest extends TestCase
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
    public function table_should_be_named_accordingly()
    {
        $this->assertEquals($this->tablename, with(new Course)->getTable());
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
    public function it_should_have_title_column()
    {
        $this->assertArrayHasKey('title', $this->columns);
        $this->assertTrue($this->columns['title']->getNotNull());
        $this->assertEquals('string', $this->db->getColumnType($this->tablename, 'title'));
    }

    /**
     * @test
     * @group  unit
     * @group  migration:courses
     * @return void
     */
    public function it_should_have_subtitle_column()
    {
        $this->assertArrayHasKey('subtitle', $this->columns);
        $this->assertFalse($this->columns['subtitle']->getNotNull());
        $this->assertEquals('text', $this->db->getColumnType($this->tablename, 'subtitle'));
    }

    /**
     * @test
     * @group  unit
     * @group  migration:courses
     * @return void
     */
    public function it_should_have_slug_column()
    {
        $this->assertArrayHasKey('slug', $this->columns);
        $this->assertTrue($this->columns['slug']->getNotNull());
        $this->assertEquals('string', $this->db->getColumnType($this->tablename, 'slug'));
    }

    /**
     * @test
     * @group  unit
     * @group  migration:courses
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
     * @group  migration:courses
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
    public function it_should_have_image_column()
    {
        $this->assertArrayHasKey('image', $this->columns);
        $this->assertFalse($this->columns['image']->getNotNull());
        $this->assertEquals('text', $this->db->getColumnType($this->tablename, 'image'));
    }

    /**
     * @test
     * @group  unit
     * @group  migration:courses
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
     * @group  migration:courses
     * @return void
     */
    public function it_should_have_category_id_column()
    {
        $this->assertArrayHasKey('category_id', $this->columns);
        $this->assertFalse($this->columns['category_id']->getNotNull());
        $this->assertEquals('integer', $this->db->getColumnType($this->tablename, 'category_id'));
    }

    /**
     * @test
     * @group  unit
     * @group  migration:courses
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
     * @group  migration:courses
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
     * @group  migration:courses
     * @return void
     */
    public function it_should_have_deleted_at_column()
    {
        $this->assertArrayHasKey('deleted_at', $this->columns);
        $this->assertFalse($this->columns['deleted_at']->getNotNull());
        $this->assertEquals('datetime', $this->db->getColumnType($this->tablename, 'deleted_at'));
    }
}
