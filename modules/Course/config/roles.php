<?php

return [
    [
        'name' => 'Learner',
        'code' => 'learner',
        'alias' => 'Student',
        'description' => 'Takes courses, tracked progress',
        'permissions' => [
            'settings.owned',
            'courses.content', 'courses.single', 'courses.show',
            'courses.favorites', 'courses.favorite', 'courses.unfavorite',
            'courses.subscriptions', 'courses.subscribe', 'courses.unsubscribe',
            'courses.progress', 'contents.complete', 'contents.show',
        ],
    ],

    [
        'name' => 'Trainer',
        'code' => 'trainer',
        'alias' => 'Instructor',
        'description' => 'Manages and publishes owned courses',
        'permissions' => [
            'settings.owned',
            'courses.owned', 'courses.trashed', 'courses.show',
            'courses.create', 'courses.store',
            'courses.edit', 'courses.update',
            'courses.destroy', 'courses.delete', 'courses.restore',
            'courses.publish', 'courses.unpublish', 'courses.draft',
            'courses.favorites', 'courses.favorite', 'courses.unfavorite',
            'courses.subscriptions', 'courses.subscribe', 'courses.unsubscribe',
        ],
    ],
];
