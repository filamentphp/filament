<?php

return [

    'label' => 'Importer :label',

    'modal' => [

        'heading' => 'Importer :label',

        'form' => [

            'file' => [
                'label' => 'Fichier',
                'placeholder' => 'Télécharger un fichier CSV',
            ],

            'columns' => [
                'label' => 'Colonnes',
                'placeholder' => 'Choisir une colonne',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Télécharger le fichier CSV exemple',
            ],

            'import' => [
                'label' => 'Importer',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Importation terminée',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Télécharger les informations sur la ligne qui a échoué|Télécharger les informations sur les lignes qui ont échoué',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Le fichier CSV est trop volumineux',
            'body' => 'Vous ne pouvez pas importer plus d\'une ligne à la fois.|Vous ne pouvez pas importer plus de :count lignes à la fois.',
        ],

        'started' => [
            'title' => 'L\'importation a commencée',
            'body' => 'Votre importation a commencé et 1 ligne sera traitée en arrière-plan.|Votre importation a commencé et :count lignes seront traitées en arrière-plan.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'Erreur système, veuillez contacter le support.',
    ],

];
