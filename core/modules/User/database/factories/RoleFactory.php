<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use User\Models\Role;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'name' => $name = $faker->unique()->words($nb = 3, $asText = true),
        'alias' => $faker->words($nb = 4, $asText = true),
        'code' => Str::slug($name),
        'description' => $faker->sentences($nb = 3, $asText = true),
    ];
});
