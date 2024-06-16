<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
// use User\Models\User;
use Widget\Models\Widget;

$factory->define(Widget::class, function (Faker $faker) {
    return [
        'file' => 'InspiringQuote.php',
        'namespace' => 'Core\Widgets',
        'fullname' => 'Core\Widgets\InspiringQuote',
        'name' => 'inspiring',
        'alias' => 'inspiring',
        'description' => 'Output an inspiring quote',
    ];
});
