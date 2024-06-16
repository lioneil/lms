<?php

return [
    /**
     *--------------------------------------------------------------------------
     * Admin Middlewares
     *--------------------------------------------------------------------------
     *
     * Define middlewares for routes prefixed with 'admin'.
     *
     * For other middlewares available,
     * see core/Http/Kernel.php
     *
     */
    'admin' => [
        'web', 'auth', 'auth.permissions', 'cache.headers:private,max-age=360;etag',
    ],

    /**
     *--------------------------------------------------------------------------
     * Public Middlewares
     *--------------------------------------------------------------------------
     *
     * Define middlewares for public or web routes.
     *
     * For other middlewares available,
     * see core/Http/Kernel.php
     *
     */
    'web' => [
        'web', 'cache.headers:public,max-age=360;etag',
    ],
];
