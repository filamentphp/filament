<?php

return [

    'label' => 'Էջավորման նավիգացիա',

    'overview' => 'Ցուցադրվում են :total արդյունքներից :first-ից :last-ը',

    'fields' => [

        'records_per_page' => [

            'label' => 'Մեկ էջում',

            'options' => [
                'all' => 'Բոլորը',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Առաջինը',
        ],

        'go_to_page' => [
            'label' => 'Գնալ էջ :page',
        ],

        'last' => [
            'label' => 'Վերջինը',
        ],

        'next' => [
            'label' => 'Հաջորդը',
        ],

        'previous' => [
            'label' => 'Նախորդը',
        ],

    ],

];
