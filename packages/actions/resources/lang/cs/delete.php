<?php

return [

    'single' => [

        'label' => 'Smazat',

        'modal' => [

            'heading' => 'Smazat :label',

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

        'label' => 'Smazat vybrané',

        'modal' => [

            'heading' => 'Smazat vybrané :label',

            'actions' => [

                'delete' => [
                    'label' => 'Smazat vybrané',
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
                'title' => 'Odstranění selhalo',
                'missing_authorization_failure_message' => 'Nemáte oprávnění odstranit :count.',
                'missing_processing_failure_message' => ':count se nepodařilo odstranit.',
            ],

        ],

    ],

];
