<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use Taxonomy\Models\Taxonomy;
use User\Models\User;

$factory->define(Taxonomy::class, function (Faker $faker) {
    return [
        'name' => $name = $faker->unique()->sentence(),
        'alias' => $faker->words(3, true),
        'code' => str_slug($name),
        'description' => $faker->paragraph(),
        'icon' => 'mdi mdi-pencil-outline',
        'type' => 'taxonomy',
        'user_id' => function () {
            return factory(User::class)->create()->getKey();
        },
    ];
});
