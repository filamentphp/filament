<?php

return [

    'single' => [

        'label' => 'Taasta',

        'modal' => [

            'heading' => 'Taasta :label',

            'actions' => [

                'restore' => [
                    'label' => 'Taasta',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Taastatud',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Taasta valitud',

        'modal' => [

            'heading' => 'Taasta valitud :label',

            'actions' => [

                'restore' => [
                    'label' => 'Taasta',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Taastatud',
            ],

            'restored_partial' => [
                'title' => 'Taastatud :count :total-st',
                'missing_authorization_failure_message' => 'Ei ole õigust taastada :count.',
                'missing_processing_failure_message' => ':count ei saanud taastada.',
            ],

            'restored_none' => [
                'title' => 'Taastamine ebaõnnestus',
                'missing_authorization_failure_message' => 'Ei ole õigust taastada :count.',
                'missing_processing_failure_message' => ':count ei saanud taastada.',
            ],

        ],

    ],

];
