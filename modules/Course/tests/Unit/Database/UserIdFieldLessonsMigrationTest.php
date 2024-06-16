<?php

namespace Course\Unit\Database;

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
class UserIdFieldLessonsMigrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->tablename = 'lessons';
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
    public function it_should_have_user_id_column()
    {
        $this->assertArrayHasKey('user_id', $this->columns);
        $this->assertFalse($this->columns['user_id']->getNotNull());
        $this->assertEquals('integer', $this->db->getColumnType($this->tablename, 'user_id'));
    }
}
