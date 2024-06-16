<?php

use Core\Enumerations\Role as RoleCode;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use User\Models\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        'prefixname' => $faker->title(),
        'firstname' => $faker->firstName(),
        'middlename' => $faker->lastName(),
        'lastname' => $faker->lastName(),
        'suffixname' => $faker->suffix(),
        'email' => $email = $faker->unique()->email(),
        'username' => Str::slug($email),
        'password' => $faker->password(),
        'type' => RoleCode::TEST,
    ];
});

$factory->state(User::class, 'superadmin', [
    'type' => RoleCode::SUPERADMIN,
]);

$factory->state(User::class, 'test', [
    'type' => RoleCode::TEST,
]);
