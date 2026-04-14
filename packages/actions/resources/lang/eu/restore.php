<?php

return [

    'single' => [

        'label' => 'Berreskuratu',

        'modal' => [

            'heading' => 'Berreskuratu :label',

            'actions' => [

                'restore' => [
                    'label' => 'Berreskuratu',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Berreskuratuta',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Hautatuak berreskuratu',

        'modal' => [

            'heading' => 'Hautatutako :label berreskuratu',

            'actions' => [

                'restore' => [
                    'label' => 'Berreskuratu',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Berreskuratuta',
            ],

            'restored_partial' => [
                'title' => ':count / :total berreskuratu dira',
                'missing_authorization_failure_message' => 'Ez duzu baimenik :count berreskuratzeko.',
                'missing_processing_failure_message' => ':count ezin izan dira berreskuratu.',
            ],

            'restored_none' => [
                'title' => 'Ezin izan da berreskuratu',
                'missing_authorization_failure_message' => 'Ez duzu baimenik :count berreskuratzeko.',
                'missing_processing_failure_message' => ':count ezin izan dira berreskuratu.',
            ],

        ],

    ],

];
