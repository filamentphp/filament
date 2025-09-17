<?php

return [

    'single' => [

        'label' => 'Gjenopprett',

        'modal' => [

            'heading' => 'Gjenopprett :label',

            'actions' => [

                'restore' => [
                    'label' => 'Gjenopprett',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Gjenopprettet',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Gjenopprett valgte',

        'modal' => [

            'heading' => 'Gjenopprett valgte :label',

            'actions' => [

                'restore' => [
                    'label' => 'Gjenopprett',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Gjenopprettet',
            ],

            'restored_partial' => [
                'title' => 'Gjenopprettet :count av :total',
                'missing_authorization_failure_message' => 'Du har ikke tillatelse til å gjenopprette :count.',
                'missing_processing_failure_message' => ':count kunne ikke gjenopprettes.',
            ],

            'restored_none' => [
                'title' => 'Kunne ikke gjenopprette',
                'missing_authorization_failure_message' => 'Du har ikke tillatelse til å gjenopprette :count.',
                'missing_processing_failure_message' => ':count kunne ikke gjenopprettes.',
            ],

        ],

    ],

];
