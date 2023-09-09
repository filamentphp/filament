<?php

return [

    'label' => 'Lapozás',

    'overview' => ':first / :last mutatása a(z) :total találatból',

    'fields' => [

        'records_per_page' => [

            'label' => 'Sorok száma:',

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
