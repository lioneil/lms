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
class MetadataFieldLessonsMigrationTest extends TestCase
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
    public function it_should_have_metadata_column()
    {
        $this->assertArrayHasKey('metadata', $this->columns);
        $this->assertFalse($this->columns['metadata']->getNotNull());
        $this->assertEquals('text', $this->db->getColumnType($this->tablename, 'metadata'));
    }
}
