<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Comment\Models\Comment;
use Faker\Generator as Faker;
use User\Models\User;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'body' => $faker->text(),
        'commentable_id' => $user = factory(User::class)->create()->getKey(),
        'commentable_type' => User::class,
        'user_id' => $user,
        'parent_id' => null,
    ];
});
