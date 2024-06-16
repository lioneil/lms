<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use Taxonomy\Models\Category;
use User\Models\User;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $name = ucwords($faker->unique()->words(4, true)),
        'alias' => $faker->words(3, true),
        'code' => str_slug($name),
        'description' => $faker->paragraph(),
        'icon' => 'mdi mdi-pencil-outline',
        'type' => 'taxonomy',
        'user_id' => factory(User::class)->create()->getKey(),
    ];
});
