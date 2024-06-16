<?php

namespace Tests\Course\Feature\Admin;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Feature\Admin
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class FavoriteCoursesTest extends TestCase
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
     * @group  feature
     * @group  feature:course
     * @group  courses.favorite
     * @return void
     */
    public function a_super_user_can_view_all_their_favorite_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asSuperAdmin());
        $courses = factory(Course::class, 2)->create();
        $favorites = factory(Course::class, 3)->create(['title' => 'Fav-'.$this->faker->word]);
        $favorites->each->favorite($user);

        // Actions
        $response = $this->get(route('courses.favorites'));

        // Assertions
        $this->assertTrue($favorites->random()->isFavoritedBy($user));
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('course::admin.favorites')
                 ->assertSeeText(trans('Favorite Courses'))
                 ->assertSeeTextInOrder($favorites->pluck('title')->toArray())
                 ->assertDontSeeText($courses->random()->title);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.favorite
     * @return void
     */
    public function a_user_can_view_all_their_favorite_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.favorites']));
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 2)->create();
        $favorites = factory(Course::class, 3)->create(['title' => 'Fav-'.$this->faker->word]);
        $favorites->each->favorite($user);

        // Actions
        $response = $this->get(route('courses.favorites'));

        // Assertions
        $this->assertTrue($favorites->random()->isFavoritedBy($user));
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('course::admin.favorites')
                 ->assertSeeText(trans('Favorite Courses'))
                 ->assertSeeTextInOrder($favorites->pluck('title')->toArray())
                 ->assertDontSeeText($courses->random()->title);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.favorite
     * @return void
     */
    public function a_logged_in_user_can_favorite_a_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.favorite']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->post(
            route('courses.favorite', $course->getKey()), [], ['HTTP_REFERER' => route('courses.index')]
        );

        // Assertions
        $response->assertRedirect(route('courses.index'));
        $this->assertTrue($course->isFavoritedBy($user));
        $this->assertDatabaseHas('favorites', $course->favorites->first()->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.favorite
     * @return void
     */
    public function a_logged_out_user_cannot_favorite_a_course()
    {
        // Arrangements
        $user = $this->asNonSuperAdmin(['courses.favorite']);
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->post(
            route('courses.favorite', $course->getKey()), [], ['HTTP_REFERER' => route('courses.index')]
        );

        // Assertions
        $response->assertRedirect(url('login'));
        $this->assertFalse($course->isFavoritedBy($user));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.unfavorite
     * @return void
     */
    public function a_logged_in_user_can_unfavorite_a_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.unfavorite']));
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();
        $this->service->find($course->getKey())->favorite();

        // Actions
        $response = $this->patch(
            route('courses.unfavorite', $course->getKey()), [], ['HTTP_REFERER' => route('courses.index')]
        );

        // Assertions
        $response->assertRedirect(route('courses.index'));
        $this->assertFalse($course->isFavoritedBy($user));
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->getKey(), 'favorite_id' => $course->getKey()
        ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.unfavorite
     * @return void
     */
    public function a_logged_out_user_cannot_unfavorite_a_course()
    {
        // Arrangements
        $user = $this->asNonSuperAdmin(['courses.favorite']);
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();
        $this->service->find($course->getKey())->favorite($user);

        // Actions
        $response = $this->patch(
            route('courses.unfavorite', $course->getKey()), [], ['HTTP_REFERER' => route('courses.index')]
        );

        // Assertions
        $response->assertRedirect(url('login'));
        $this->assertTrue($course->isFavoritedBy($user));
    }
}
