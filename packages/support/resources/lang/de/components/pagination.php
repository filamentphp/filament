<?php

return [

    'label' => 'Seitennavigation',

    'overview' => '{1} Zeige 1 Ergebnis|[2,*] Zeige :first bis :last von :total Ergebnissen',

    'fields' => [

        'records_per_page' => [

            'label' => 'pro Seite',

            'options' => [
                'all' => 'Alle',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Erste',
        ],

        'go_to_page' => [
            'label' => 'Weiter zur Seite :page',
        ],

        'last' => [
            'label' => 'Letzte',
        ],

        'next' => [
            'label' => 'Nächste',
        ],

        'previous' => [
            'label' => 'Vorherige',
        ],

    ],

];
