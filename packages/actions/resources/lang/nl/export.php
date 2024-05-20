<?php

return [

    'label' => ':Label exporteren',

    'modal' => [

        'heading' => ':Label exporteren',

        'form' => [

            'columns' => [

                'label' => 'Kolommen',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column ingeschakeld',
                    ],

                    'label' => [
                        'label' => ':column label',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Exporteren',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Exporteren voltooid',

            'actions' => [

                'download_csv' => [
                    'label' => '.csv downloaden',
                ],

                'download_xlsx' => [
                    'label' => '.xlsx downloaden',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Export is te groot',
            'body' => 'Je mag niet meer dan 1 rij tegelijk exporteren.|Je mag niet meer dan :count rijen tegelijk exporteren.',
        ],

        'started' => [
            'title' => 'Exporteren gestart',
            'body' => 'Je export is begonnen en 1 rij wordt op de achtergrond verwerkt. Je ontvangt een melding met de downloadlink wanneer deze is voltooid.|Je export is begonnen en :count rijen worden op de achtergrond verwerkt. Je ontvangt een melding met de downloadlink wanneer deze is voltooid.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
