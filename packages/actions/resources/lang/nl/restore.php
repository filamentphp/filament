<?php

return [

    'single' => [

        'label' => 'Herstellen',

        'modal' => [

            'heading' => ':Label herstellen',

            'actions' => [

                'restore' => [
                    'label' => 'Herstellen',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Hersteld',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Geselecteerde herstellen',

        'modal' => [

            'heading' => 'Geselecteerde :label herstellen',

            'actions' => [

                'restore' => [
                    'label' => 'Herstellen',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Hersteld',
            ],

            'restored_partial' => [
                'title' => ':count van :total hersteld',
                'missing_authorization_failure_message' => 'Je hebt geen toestemming om :count te herstellen.',
                'missing_processing_failure_message' => ':count kon niet worden hersteld.',
            ],

            'restored_none' => [
                'title' => 'Failed to restore',
                'missing_authorization_failure_message' => 'Je hebt geen toestemming om :count te herstellen.',
                'missing_processing_failure_message' => ':count kon niet worden hersteld.',
            ],

        ],

    ],

];
