<?php

return [

    'label' => 'Pagination navigation',

    'overview' => '{1} Showing 1 result|[2,*] Showing :first to :last of :total results',

    'fields' => [

        'records_per_page' => [

            'label' => 'Per page',

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
