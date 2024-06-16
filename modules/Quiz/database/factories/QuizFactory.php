<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Core\Models\Template;
use Faker\Generator as Faker;
use Quiz\Models\Quiz;
use User\Models\User;

$factory->define(Quiz::class, function (Faker $faker) {
    return [
        'title' => $title = ucwords($faker->unique()->words(4, true)),
        'subtitle' => $faker->sentence(),
        'description' => $faker->paragraph(),
        'slug' => str_slug($title),
        'url' => $faker->url(),
        'metadata' => $faker->unique()->word(),
        'method' => $faker->sentence(),
        'template_id' => factory(Template::class)->create()->getKey(),
        'user_id' => factory(User::class)->create()->getKey(),
    ];
});
