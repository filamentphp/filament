<?php

return [

    'label' => 'Lapozás',

    'overview' => '{1} 1 találat|[2,*] :first - :last megjelenítve a(z) :total találatból',

    'fields' => [

        'records_per_page' => [

            'label' => 'Sorok száma:',

            'options' => [
                'all' => 'Összes',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Első',
        ],

        'go_to_page' => [
            'label' => 'Ugrás az oldalra: :page',
        ],

        'last' => [
            'label' => 'Utolsó',
        ],

        'next' => [
            'label' => 'Következő',
        ],

        'previous' => [
            'label' => 'Előző',
        ],

    ],

];
