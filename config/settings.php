<?php

return [

    /**
     *--------------------------------------------------------------------------
     * Settings Configuration
     *--------------------------------------------------------------------------
     *
     * You may define some initial application settings here. Upon seeding, the
     * items here will be saved to the database.
     *
     */

    'app:title' => env('APP_NAME', 'Pluma'),
    'app:tagline' => env('APP_TAGLINE', null),
    'app:author' => env('APP_AUTHOR', 'Pluma'),
    'app:email' => env('APP_EMAIL', 'email@domain.io'),
    'app:year' => env('APP_YEAR', date('Y')),
    'app:theme' => env('APP_THEME', 'default'),
    'app:copyright' => env('APP_COPYRIGHT'),

    /**
     * Possible values:
     *
     * - 'd-M, Y'    - or any PHP date format syntax.
     * - :human:     - will parse the value to Carbon::diffForHumans.
     *
     */
    'format:date' => ':human:',
    'format:time' => 'h:i a',
    'format:datetime' => 'd-M, Y h:i a',
    'formal:date' => 'd-M Y',
    'formal:time' => 'h:i A',
    'formal:datetime' => 'd-M Y h:i A',

    'display:perpage' => 15,

    'storage:modules' => 'modules',
];
