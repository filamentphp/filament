<?php

return [

    'label' => 'Eksportuj :label',

    'modal' => [

        'heading' => 'Eksportuj :label',

        'form' => [

            'columns' => [

                'label' => 'Kolumny',

                'actions' => [

                    'select_all' => [
                        'label' => 'Zaznacz wszystko',
                    ],

                    'deselect_all' => [
                        'label' => 'Odznacz wszystko',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => ':column włączony',
                    ],

                    'label' => [
                        'label' => 'Etykieta :column',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Eksportuj',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Eksport zakończony',

            'actions' => [

                'download_csv' => [
                    'label' => 'Pobierz .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Pobierz .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Zbyt dużo wierszy',
            'body' => 'Nie możesz wyeksportować więcej niż 1 wiersz na raz.|Nie możesz wyeksportować więcej niż :count wierszy na raz.',
        ],

        'no_columns' => [
            'title' => 'Brak wybranych kolumn',
            'body' => 'Proszę wybrać przynajmniej jedną kolumnę do eksportu.',
        ],

        'started' => [
            'title' => 'Eksport rozpoczęty',
            'body' => 'Rozpoczęto eksport i 1 wiersz zostanie przetworzony w tle.|Rozpoczęto eksport i :count wierszy zostanie przetworzonych w tle.',
        ],

    ],

    'file_name' => 'eksport-:export_id-:model',

];
