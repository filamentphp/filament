<?php

return [

    'label' => 'Exporter :label',

    'modal' => [

        'heading' => 'Exporter :label',

        'form' => [

            'columns' => [

                'label' => 'Colonnes',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column sélectionnée',
                    ],

                    'label' => [
                        'label' => ':column label',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Exporter',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Exportation terminée',

            'actions' => [

                'download_csv' => [
                    'label' => 'Télécharger .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Télécharger .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'L\'exportation est trop volumineuse',
            'body' => 'Vous ne pouvez pas exporter plus d’une ligne à la fois.|Vous ne pouvez pas exporter plus de :count lignes à la fois',
        ],

        'started' => [
            'title' => 'L’exportation a commencé',
            'body' => 'Votre exportation a commencé et 1 ligne sera traitée en arrière-plan.|Votre exportation a commencé et :count lignes seront traitées en arrière-plan.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
