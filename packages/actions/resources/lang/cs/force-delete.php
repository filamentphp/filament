<?php

return [

    'single' => [

        'label' => 'Trvale smazat',

        'modal' => [

            'heading' => 'Trvale smazat :label',

            'actions' => [

                'delete' => [
                    'label' => 'Smazat',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Smazáno',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Trvale smazat vybrané',

        'modal' => [

            'heading' => 'Trvale smazat vybrané :label',

            'actions' => [

                'delete' => [
                    'label' => 'Smazat',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Smazáno',
            ],

            'deleted_partial' => [
                'title' => 'Odstraněno :count z :total',
                'missing_authorization_failure_message' => 'Nemáte oprávnění odstranit :count.',
                'missing_processing_failure_message' => ':count se nepodařilo odstranit.',
            ],

            'deleted_none' => [
                'title' => 'Odstranění se nezdařilo',
                'missing_authorization_failure_message' => 'Nemáte oprávnění odstranit :count.',
                'missing_processing_failure_message' => ':count se nepodařilo odstranit.',
            ],

        ],

    ],

];
