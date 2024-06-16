<?php

return [
    'dashboard' => [
        'name' => 'dashboard',
        'order' => 5,
        'route:name' => 'dashboard',
        'icon' => 'mdi mdi-view-dashboard-outline',
        'always:viewable' => true,
        'text' => 'Dashboard',
        'description' => 'View app overview and summary.',
        'permissions' => '*',
    ],

    'header:content' => [
        'name' => 'header:content',
        'is:header' => true,
        'always:viewable' => true,
        'order' => 20,
        'class' => 'separator',
        'markup' => 'span',
        'text' => 'Content',
    ],
];
