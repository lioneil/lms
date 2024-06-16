<?php

namespace Tests\Unit\Application\Repository;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @package Tests\Unit\Application\Repository
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class RepositoryTest extends TestCase
{
    use DatabaseMigrations,
        RefreshDatabase,
        WithFaker;

    /**
     * @test
     * @group  unit
     * @group  unit:repository
     */
    public function testItCanRetrieveAllModels()
    {
        # Arrangements
        $users = factory(\User\Models\User::class, 10)->create();
        $repository = new \Core\Application\Repository\Repository(new \User\Models\User, app('request'));

        # Actions
        $resources = $repository->all();

        # Assertions
        $this->assertInstanceOf(\User\Models\User::class, $resources->random());
        $this->assertEquals(count($users), $resources->count());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:repository
     */
    public function testItCanGetModelsWithParameters()
    {
        # Arrangements
        factory(\User\Models\User::class, 10)->create();
        $username = $this->faker()->unique()->email();
        $user = factory(\User\Models\User::class)->create(['username' => $username]);
        $repository = new \Core\Application\Repository\Repository(new \User\Models\User, app('request'));

        # Actions
        $resources = $repository->where('username', $username)->get();

        # Assertions
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $resources);
        $this->assertEquals($user->username, $resources->random()->username);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:repository
     */
    public function testItCanStoreToDatabaseAndReturnTheLastSavedData()
    {
        # Arrangements
        $attributes = factory(\User\Models\User::class)->make()->makeVisible('password')->toArray();
        $database = with(new \User\Models\User)->getTable();
        $repository = new \Core\Application\Repository\Repository(new \User\Models\User, app('request'));

        # Actions
        $resource = $repository->store($attributes);

        # Assertions
        $this->assertDatabaseHas($database, $attributes);
        $this->assertEquals($attributes['firstname'], $resource->firstname);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:repository
     */
    public function testItCanUpdateAndReturnTheLastUpdatedData()
    {
        # Arrangements
        $attributes = factory(\User\Models\User::class)->create()->makeVisible('password');
        $database = with(new \User\Models\User)->getTable();
        $repository = new \Core\Application\Repository\Repository(new \User\Models\User, app('request'));
        $firstname = $this->faker()->firstName();

        # Actions
        $repository->update($attributes->id, ['firstname' => $firstname]);

        # Assertions
        $this->assertDatabaseHas($database, ['firstname' => $firstname]);
    }
}
