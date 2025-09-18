<?php

return [

    'label' => 'Pagination navigation',

    'overview' => '{1} Result 1 tihlan|[2,*] Result :first atang :last lanna, :total atangin',

    'fields' => [

        'records_per_page' => [

            'label' => 'Page tinah',

            'options' => [
                'all' => 'A vaiin',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'A hmasa ber',
        ],

        'go_to_page' => [
            'label' => 'Go to page :page',
        ],

        'last' => [
            'label' => 'A tawpna',
        ],

        'next' => [
            'label' => 'A dawttu',
        ],

        'previous' => [
            'label' => 'A hmasa',
        ],

    ],

];
