<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use Library\Models\Library;
use User\Models\User;

$factory->define(Library::class, function (Faker $faker) {
    return [
        'name' => $faker->name(),
        'url' => $faker->url(),
        'pathname' => $faker->url(),
        'size' => $faker->randomDigitNotNull(),
        'type' => 'library',
        'user_id' => factory(User::class)->create()->getKey(),
    ];
});
