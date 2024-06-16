<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Subscription\Models\Subscription;
use User\Models\User;

$factory->define(Subscription::class, function (Faker $faker) {
    return [
        'name' => Str::title($faker->words($nb = 3, $asText = true)),
        'subscribable_id' => $userId = factory(User::class)->create()->getKey(),
        'subscribable_type' => User::class,
        'subscribed_at' => $faker->datetime(),
        'user_id' => $userId,
    ];
});
