<?php

return [

    'label' => 'Pagination navigation',

    'overview' => '{1} Result 1 tihlan|[2,*] Result :first atang :last lanna, :total atangin',

    'fields' => [

        'records_per_page' => [

            'label' => 'Page tinah',

            'options' => [
                'all' => 'All',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'First',
        ],

        'go_to_page' => [
            'label' => 'Go to page :page',
        ],

        'last' => [
            'label' => 'Last',
        ],

        'next' => [
            'label' => 'Next',
        ],

        'previous' => [
            'label' => 'Previous',
        ],

    ],

];
