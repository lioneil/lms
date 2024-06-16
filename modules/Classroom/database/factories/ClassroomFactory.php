<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Classroom\Models\Classroom;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use User\Models\User;

$factory->define(Classroom::class, function (Faker $faker) {
    return [
        'name' => $title = Str::title($faker->unique()->words($count = 4, $asText = true)),
        'code' => Str::slug($title),
        'description' => $faker->paragraph(),
        'user_id' => factory(User::class)->create()->getKey(),
    ];
});
