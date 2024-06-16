<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Menu\Models\Menu;
use User\Models\User;

$factory->define(Menu::class, function (Faker $faker) {
    return [
        'title' => $title = Str::title($faker->unique()->words($count = 4, $asText = true)),
        'uri' => Str::slug($title),
        'location' => Str::slug($title),
        'icon' => 'mdi mdi-pencil-outline',
        'sort' => '0',
        'parent' => Str::slug($title),
        'menuable_type' => User::class,
        'menuable_id' => factory(User::class)->create()->getKey(),
    ];
});
