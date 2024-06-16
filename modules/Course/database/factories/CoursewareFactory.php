<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Course\Models\Course;
use Course\Models\Courseware;
use Faker\Generator as Faker;
use User\Models\User;

$factory->define(Courseware::class, function (Faker $faker) {
    return [
        'title' => $faker->words($nb = 4, $asText = true),
        'uri' => $faker->slug(),
        'pathname' => $faker->url(),
        'coursewareable_id' => factory(Course::class)->create()->getKey(),
        'coursewareable_type' => Course::class,
        'type' => 'courseware',
        'user_id' => factory(User::class)->create()->getKey(),
    ];
});
