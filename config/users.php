<?php

return [
    'superadmins' => [
        [
            'firstname' => env('APP_AUTHOR'),
            'lastname' => env('APP_NAME'),
            'username' => env('APP_EMAIL', 'superadmin'),
            'email' => env('APP_EMAIL', 'email@domain.io'),
            'password' => 'superadmin',
        ],
    ],
];
