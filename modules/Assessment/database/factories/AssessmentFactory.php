<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Assessment\Models\Assessment;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Template\Models\Template;
use User\Models\User;

$factory->define(Assessment::class, function (Faker $faker) {
    return [
        'title' => $title = Str::title($faker->unique()->words($nb = 3, $asText = true)),
        'subtitle' => Str::slug($title),
        'description' => $faker->paragraph(),
        'slug' => Str::slug($title),
        'url' => $faker->url(),
        'method' => 'post',
        'type' => 'form',
        'metadata' => '',
        'template_id' => factory(Template::class)->create()->getKey(),
        'user_id' => factory(User::class)->create()->getKey(),
    ];
});
