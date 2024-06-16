<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Course\Models\Student;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Student::class, function (Faker $faker) {
    return [
        'prefixname' => $faker->title(),
        'firstname' => $faker->firstName(),
        'middlename' => $faker->lastName(),
        'lastname' => $faker->lastName(),
        'suffixname' => $faker->suffix(),
        'email' => $email = $faker->unique()->email(),
        'username' => Str::slug($email),
        'password' => $faker->password(),
        'type' => 'student',
    ];
});
