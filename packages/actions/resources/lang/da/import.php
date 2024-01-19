<?php

return [

    'label' => 'Importer :label',

    'modal' => [

        'heading' => 'Importer :label',

        'form' => [

            'file' => [
                'label' => 'Fil',
                'placeholder' => 'Upload en CSV fil',
            ],

            'columns' => [
                'label' => 'Kolonner',
                'placeholder' => 'Vælg en kolonne',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Download eksempel på CSV fil',
            ],

            'import' => [
                'label' => 'Importer',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Import afsluttet',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Download information om rækken med fejl|Download information om rækkerne med fejl',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Uploadet CSV fil er for stor',
            'body' => 'Du må ikke importere mere end 1 række på én gang.|Du må ikke importere mere end :count rækker på én gang.',
        ],

        'started' => [
            'title' => 'Import started',
            'body' => 'Din import er begyndt, og 1 række vil blive behandlet i baggrunden.|Din import er begyndt, og :count rækker vil blive behandlet i baggrunden',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'fejl',
        'system_error' => 'Systemfejl, kontakt venligst support.',
    ],

];
