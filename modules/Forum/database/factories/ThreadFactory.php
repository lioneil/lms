<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Forum\Models\Thread;
use Illuminate\Support\Str;
use Taxonomy\Models\Category;
use User\Models\User;

$factory->define(Thread::class, function (Faker $faker) {
    return [
        'title' => $title = Str::title($faker->unique()->words($count = 4, $asText = true)),
        'slug' => Str::slug($title),
        'body' => $faker->text(),
        'type' => 'thread',
        'user_id' => factory(User::class)->create()->getKey(),
        'category_id' => factory(Category::class)->create()->getKey(),
    ];
});
