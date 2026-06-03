<?php

return [
    'default' => 'main',
    'connections' => [
        'main' => [
            'salt' => env('HASHIDS_SALT', 'sikema-salt-2026'),
            'length' => 8,
        ],
    ],
];
