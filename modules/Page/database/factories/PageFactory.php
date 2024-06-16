<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Core\Models\Template;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Page\Models\Page;
use Taxonomy\Models\Category;
use User\Models\User;

$factory->define(Page::class, function (Faker $faker) {
    return [
        'title' => $title = Str::title($faker->unique()->words($count = 4, $asText = true)),
        'code' => Str::slug($title),
        'feature' => $faker->text(),
        'body' => $faker->text(),
        'template_id' => function () {
            return factory(Template::class)->create()->getKey();
        },
        'user_id' => factory(User::class)->create()->getKey(),
        'category_id' => factory(Category::class)->create()->getKey(),
    ];
});
