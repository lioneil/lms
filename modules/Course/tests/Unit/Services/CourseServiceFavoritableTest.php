<?php

namespace Tests\Course\Unit\Services;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Course\Unit\Services
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CourseServiceFavoritableTest extends TestCase
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
     * @group  service
     * @group  course:favorite
     * @return void
     */
    public function it_can_set_a_course_as_favorite_of_the_logged_in_user()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.favorite']));
        $this->withPermissionsPolicy();
        $otherUser = factory(User::class)->create();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $this->service->find($course->getKey())->favorite();
        $course = $this->service->find($course->getKey());

        // Assertions
        $this->assertTrue($course->isFavoritedBy($user));
        $this->assertFalse($course->isFavoritedBy($otherUser));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  course:favorite
     * @return void
     */
    public function it_can_set_a_course_as_favorite_of_the_passed_in_user()
    {
        // Arrangements
        $user = factory(User::class)->create();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $this->service->find($course->getKey())->favorite($user);
        $course = $this->service->find($course->getKey());

        // Assertions
        $this->assertTrue($course->isFavoritedBy($user));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  course:favorite
     * @return void
     */
    public function it_can_set_a_course_as_favorite_of_a_user_only_once()
    {
        // Arrangements
        $user = factory(User::class)->create();
        $course = factory(Course::class, 3)->create()->random();
        $expected = 1;

        // Actions
        $this->service->find($course->getKey())->favorite($user);
        $this->service->find($course->getKey())->favorite($user);
        $course = $this->service->find($course->getKey());

        // Assertions
        $this->assertTrue($course->isFavoritedBy($user));
        $this->assertEquals($expected, $course->favorites->count());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  course:unfavorite
     * @return void
     */
    public function it_can_set_a_course_as_unfavorite_of_the_logged_in_user()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.favorite']));
        $this->withPermissionsPolicy();
        $otherUser = factory(User::class)->create();
        $course = factory(Course::class, 3)->create()->random();
        $this->service->find($course->getKey())->favorite();
        $this->service->find($course->getKey())->favorite($otherUser);

        // Actions
        $this->service->find($course->getKey())->unfavorite();
        $course = $this->service->find($course->getKey());

        // Assertions
        $this->assertFalse($course->isFavoritedBy($user));
        $this->assertTrue($course->isFavoritedBy($otherUser));
        $this->assertDatabaseMissing($course->favorites->random()->getTable(), [
            'user_id' => $user->getKey(),
            'favoritable_id' => $course->getKey(),
            'favoritable_type' => get_class($course),
        ]);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  course:unfavorite
     * @return void
     */
    public function it_can_set_a_course_as_unfavorite_of_the_passed_in_user()
    {
        // Arrangements
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $course = factory(Course::class, 3)->create()->random();
        $this->service->find($course->getKey())->favorite($user);
        $this->service->find($course->getKey())->favorite($otherUser);

        // Actions
        $this->service->find($course->getKey())->unfavorite($user);
        $course = $this->service->find($course->getKey());

        // Assertions
        $this->assertFalse($course->isFavoritedBy($user));
        $this->assertTrue($course->isFavoritedBy($otherUser));
        $this->assertDatabaseMissing($course->favorites->random()->getTable(), [
            'user_id' => $user->getKey(),
            'favoritable_id' => $course->getKey(),
            'favoritable_type' => get_class($course),
        ]);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  course:unfavorite
     * @return void
     */
    public function it_can_set_a_course_as_unfavorite_of_a_user_only_once()
    {
        // Arrangements
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $course = factory(Course::class, 3)->create()->random();
        $this->service->find($course->getKey())->favorite($user);
        $this->service->find($course->getKey())->favorite($otherUser);

        // Actions
        $this->service->find($course->getKey())->unfavorite($user);
        $this->service->find($course->getKey())->unfavorite($user);
        $course = $this->service->find($course->getKey());

        // Assertions
        $this->assertFalse($course->isFavoritedBy($user));
        $this->assertTrue($course->isFavoritedBy($otherUser));
        $this->assertDatabaseMissing($course->favorites->random()->getTable(), [
            'user_id' => $user->getKey(),
            'favoritable_id' => $course->getKey(),
            'favoritable_type' => get_class($course),
        ]);
    }
}
