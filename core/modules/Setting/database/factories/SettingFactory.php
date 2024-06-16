<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use Setting\Models\Setting;
use User\Models\User;

$factory->define(Setting::class, function (Faker $faker) {
    return [
        'key' => $key = ucwords($faker->unique()->words(1, true)),
        'value' => $faker->paragraph(),
        'icon' => $faker->name(),
        'status' => $faker->text(),
        'type' => 'settings',
        'user_id' => factory(User::class)->create()->getKey(),
    ];
});
