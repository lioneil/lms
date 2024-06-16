<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Announcement\Models\Announcement;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Taxonomy\Models\Category;
use User\Models\User;

$factory->define(Announcement::class, function (Faker $faker) {
    return [
        'title' => $title = ucwords($faker->unique()->words(4, true)),
        'slug' =>  Str::slug($title),
        'body' => $faker->paragraph(),
        'photo' =>  $faker->imageUrl(),
        'pathname' => $faker->url(),
        'type' => 'announcement',
        'user_id' => factory(User::class)->create()->getKey(),
        'category_id' => factory(Category::class)->create()->getKey(),
        'published_at' => $faker->datetime(),
        'expired_at' => $faker->datetime(),
    ];
});
