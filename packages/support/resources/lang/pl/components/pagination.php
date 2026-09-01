<?php

return [

    'label' => 'Paginacja',

    'overview' => 'Pozycje od :first do :last z :total łącznie',

    'fields' => [

        'records_per_page' => [

            'label' => 'na stronę',

            'options' => [
                'all' => 'Wszystkie',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Pierwsza',
        ],

        'go_to_page' => [
            'label' => 'Przejdź do strony :page',
        ],

        'last' => [
            'label' => 'Osatnia',
        ],

        'next' => [
            'label' => 'Następna',
        ],

        'previous' => [
            'label' => 'Poprzednia',
        ],

    ],

];
