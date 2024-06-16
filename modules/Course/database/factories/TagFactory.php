<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Course\Models\Tag;
use Faker\Generator as Faker;

$factory->define(Tag::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word(),
        'icon' => 'mdi mdi-pencil-outline',
        'type' => 'course',
    ];
});
