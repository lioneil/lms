<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Template\Models\Template;
use User\Models\User;

$factory->define(Template::class, function (Faker $faker) {
    return [
        'name' => $faker->name(),
        'code' => Str::slug($faker->words(3, $string = true)),
        'pathname' => $faker->slug(),
        'user_id' => function () {
            return factory(User::class)->create()->getKey();
        },
    ];
});
