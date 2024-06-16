<?php

return [
    /**
     *--------------------------------------------------------------------------
     * Enabled Modules
     *--------------------------------------------------------------------------
     * Here we can specify all enabled modules manually. Status of enabled
     * modules are overridden by the database.
     *
     * By default, all modules are enabled. So no need to write it here.
     */
    'enabled' => [
        // '*'
    ],

    /**
     *--------------------------------------------------------------------------
     * Disabled Modules
     *--------------------------------------------------------------------------
     * It's here so we can keep track of all modules currently registered but
     * disabled. Status of disabled modules are overridden by the database.
     *
     * By default, a module's status is checked in the database first. The app
     * will only look here if no module entry was found there.
     */
    'disabled' => [
        'Calendar',
        'Note',
        'Form',
    ],
];
