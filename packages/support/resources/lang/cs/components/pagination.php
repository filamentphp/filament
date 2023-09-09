<?php

return [

    'label' => 'Stránkování',

    'overview' => '{1} Zobrazuji 1 výsledek|[2,*] Zobrazuji :first až :last z :total výsledků',

    'fields' => [

        'records_per_page' => [
            'label' => 'na stránku',

            'options' => [
                'all' => 'Vše',
            ],
        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Jít na stránku :page',
        ],

        'next' => [
            'label' => 'Další',
        ],

        'previous' => [
            'label' => 'Předchozí',
        ],

    ],

];
