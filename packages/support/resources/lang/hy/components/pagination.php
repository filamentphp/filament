<?php

return [

    'label' => 'Էջավորման նավիգացիա',

    'overview' => '{1} Ցուցադրվում է 1 արդյունք|[2,*] Ցուցադրվում են :first-ից :last-ը՝ ընդհանուր :total արդյունքից',

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
