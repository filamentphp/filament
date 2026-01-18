<?php

return [

    'single' => [

        'label' => 'Vrati',

        'modal' => [

            'heading' => 'Vrati :label',

            'actions' => [

                'restore' => [
                    'label' => 'Vrati',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Vrateno',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Vrati izabrani',

        'modal' => [

            'heading' => 'Vrati izabrani :label',

            'actions' => [

                'restore' => [
                    'label' => 'Vrati',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Vrateno',
            ],

            'restored_partial' => [
                'title' => 'Vrateni :count od :total',
                'missing_authorization_failure_message' => 'Nemate dozvola da vratite :count.',
                'missing_processing_failure_message' => ':count ne možaa da bidat vrateni.',
            ],

            'restored_none' => [
                'title' => 'Neuspešno vrakjanje',
                'missing_authorization_failure_message' => 'Nemate dozvola da vratite :count.',
                'missing_processing_failure_message' => ':count ne možaa da bidat vrateni.',
            ],

        ],

    ],

];
