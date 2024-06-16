<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Assessment\Models\Assessment;
use Assessment\Models\Submission;
use Faker\Generator as Faker;
use User\Models\User;

$factory->define(Submission::class, function (Faker $faker) {
    return [
        'results' => $faker->randomDigitNotNull(),
        'remarks' => $faker->words($nb = 3, $asText = true),
        'submissible_id' => factory(Assessment::class)->create()->getKey(),
        'submissible_type' => Assessment::class,
        'user_id' => function () {
            return factory(User::class)->create()->getKey();
        },
    ];
});
