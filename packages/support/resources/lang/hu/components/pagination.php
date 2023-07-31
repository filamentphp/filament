<?php

return [

    'label' => 'Lapozás',

    'overview' => ':first től :last ig mutatása a :total találatból',

    'fields' => [

        'records_per_page' => [

            'label' => 'oldalanként',

            'options' => [
                'all' => 'Összes',
            ],
        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Ugrás az oldalra: :page',
        ],

        'next' => [
            'label' => 'Következő',
        ],

        'previous' => [
            'label' => 'Előző',
        ],

    ],

];
