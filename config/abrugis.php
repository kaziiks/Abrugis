<?php

return [
    'calculator' => [
        'base_options' => [
            'standard' => 18.00,
            'reinforced' => 28.00,
        ],
        'removal_price_per_m2' => 8.00,
        'maximum_area_m2' => 100000,
    ],

    'admin' => [
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],
];
