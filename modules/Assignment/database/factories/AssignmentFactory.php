<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Assignment\Models\Assignment;
use Course\Models\Course;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;
use User\Models\User;

$factory->define(Assignment::class, function (Faker $faker) {
    return [
        'title' => ucwords($faker->unique()->words(4, $asText = true)),
        'uri' => $file = UploadedFile::fake()->create('tmp.text'),
        'pathname' => str_slug($file),
        'coursewareable_id' => function () {
            return factory(Course::class)->create()->getKey();
        },
        'coursewareable_type' => get_class(new Course),
        'type' => 'assignment',
        'user_id' => function () {
            return factory(User::class)->create()->getKey();
        },
    ];
});
