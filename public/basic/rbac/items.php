<?php
return [
    'login' => [
        'type' => 2,
    ],
    'logout' => [
        'type' => 2,
    ],
    'error' => [
        'type' => 2,
    ],
    'sign-up' => [
        'type' => 2,
    ],
    'index' => [
        'type' => 2,
    ],
    'view' => [
        'type' => 2,
    ],
    'update' => [
        'type' => 2,
    ],
    'create' => [
        'type' => 2,
    ],
    'about' => [
        'type' => 2,
    ],
    'delete' => [
        'type' => 2,
    ],
    'guest' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'login',
            'logout',
            'error',
            'sign-up',
            'index',
            'view',
            'create',
            'about',
        ],
    ],
    'user' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'update',
            'guest',
            'updateOwnProfile',
        ],
    ],
    'moderator' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'update',
            'guest',
            'updateOwnProfile',
        ],
    ],
    'admin' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'delete',
            'moderator',
            'user',
        ],
    ],
    'updateOwnProfile' => [
        'type' => 2,
        'ruleName' => 'isProfileOwner',
    ],
];
