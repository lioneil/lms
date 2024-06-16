<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Course\Models\Course;
use Course\Models\Lesson;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use User\Models\User;

$factory->define(Lesson::class, function (Faker $faker) {
    return [
        'title' => $title = Str::title($faker->words(5, $asText = true)),
        'subtitle' => $faker->words(10, $asText = true),
        'slug' => Str::slug($title),
        'description' => $faker->paragraph(),
        'content' => $faker->imageUrl(),
        'sort' => $faker->randomDigitNotNull(),
        'type' => 'content',
        'course_id' => factory(Course::class)->create()->getKey(),
        'user_id' => factory(User::class)->create()->getKey(),
    ];
});
