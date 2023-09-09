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

        'go_to_page' => [
            'label' => 'Przejdź do strony :page',
        ],

        'next' => [
            'label' => 'Następna',
        ],

        'previous' => [
            'label' => 'Poprzednia',
        ],

    ],

];
