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
                'title' => 'Vraćeno',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Povrati izabrano',

        'modal' => [

            'heading' => 'Povrati izabrano :label',

            'actions' => [

                'restore' => [
                    'label' => 'Povrati',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Povraćeno',
            ],

            'restored_partial' => [
                'title' => 'Povraćeno :count od :total',
                'missing_authorization_failure_message' => 'Nemate dozvolu da povratite :count.',
                'missing_processing_failure_message' => ':count ne može da se povrati.',
            ],

            'restored_none' => [
                'title' => 'Vraćanje nije uspelo',
                'missing_authorization_failure_message' => 'Nemate dozvolu da povratite :count.',
                'missing_processing_failure_message' => ':count ne može da se povrati.',
            ],
        ],

    ],

];
