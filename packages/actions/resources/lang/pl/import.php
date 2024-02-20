<?php

return [

    'label' => 'Importuj :label',

    'modal' => [

        'heading' => 'Importuj :label',

        'form' => [

            'file' => [
                'label' => 'Plik',
                'placeholder' => 'Wybierz plik CSV',
            ],

            'columns' => [
                'label' => 'Kolumny',
                'placeholder' => 'Wybierz kolumnę',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Pobierz przykładowy plik CSV',
            ],

            'import' => [
                'label' => 'Importuj',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Import zakończony',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Pobierz informacje o niepowodzeniach',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Przesłany plik CSV jest zbyt duży',
            'body' => 'Nie możesz zaimportować więcej niż 1 wiersz na raz.|Nie możesz zaimportować więcej niż :count wierszy na raz.',
        ],

        'started' => [
            'title' => 'Import rozpoczęty',
            'body' => 'Import rozpoczęty i 1 wiersz zostanie przetworzony w tle.|Import rozpoczęty i :count wierszy zostanie przetworzonych w tle.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'błąd',
        'system_error' => 'Błąd systemu, skontaktuj się z pomocą techniczną.',
    ],

];
