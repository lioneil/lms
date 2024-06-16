<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Course\Models\Course;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Material\Models\Material;
use User\Models\User;

$factory->define(Material::class, function (Faker $faker) {
    return [
        'title' => $title = Str::title($faker->unique()->words($count = 4, $asText = true)),
        'uri' => $file = $faker->sentence(),
        'pathname' => Str::slug($file),
        'coursewareable_id' => factory(Course::class)->create()->getKey(),
        'coursewareable_type' => Course::class,
        'type' => 'material',
        'user_id' => factory(User::class)->create()->getKey(),
    ];
});
