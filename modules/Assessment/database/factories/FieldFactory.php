<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Assessment\Models\Assessment;
use Assessment\Models\Field;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Field::class, function (Faker $faker) {
    return [
        'title' => $title = Str::title($faker->unique()->words($nb = 3, $asText = true)),
        'code' => Str::slug($title),
        'type' => 'text',
        'metadata' => '',
        'form_id' => factory(Assessment::class)->create()->getKey(),
        'group' => $faker->sentence(),
        'sort' => $faker->randomDigitNotNull(),
    ];
});
