<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use Taxonomy\Models\Tag;

$factory->define(Tag::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word(),
        'icon' => 'mdi mdi-pencil-outline',
        'type' => 'tag',
    ];
});
