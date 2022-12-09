<?php

return [
    'models'        => [

        'footprint' => Brokecode\Footprint\Models\Footprint::class,
    ],

    'table_names'   => [

        'foot_prints' => 'foot_prints',

    ],

    'enabled'       => env('FOOT_PRINT_ENABLED', true),
];
