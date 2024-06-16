<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Course\Models\Course;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Taxonomy\Models\Category;
use User\Models\User;

$factory->define(Course::class, function (Faker $faker) {
    return [
        'title' => $title = Str::title($faker->unique()->words($count = 4, $asText = true)),
        'subtitle' => $faker->sentence(),
        'slug' => Str::slug($title),
        'code' => Str::slug($title),
        'description' => $faker->paragraph(),
        'icon' => 'mdi mdi-pencil-outline',
        'image' => $faker->imageUrl(),
        'user_id' => factory(User::class)->create()->getKey(),
        'category_id' => factory(Category::class)->create(['type' => 'course'])->getKey(),
    ];
});

$factory->state(Course::class, 'required only', [
    'subtitle' => null,
    'description' => null,
    'icon' => null,
    'image' => null,
    'category_id' => null,
]);
