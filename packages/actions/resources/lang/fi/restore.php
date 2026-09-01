<?php

return [

    'single' => [

        'label' => 'Palauta',

        'modal' => [

            'heading' => 'Palauta :label',

            'actions' => [

                'restore' => [
                    'label' => 'Palauta',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Palautettu',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Palauta valitut',

        'modal' => [

            'heading' => 'Palauta valitut :label',

            'actions' => [

                'restore' => [
                    'label' => 'Palauta',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Palautettu',
            ],

            'restored_partial' => [
                'title' => 'Palautettu :count / :total',
                'missing_authorization_failure_message' => 'Sinulla ei ole oikeuksia palauttaa :count.',
                'missing_processing_failure_message' => ':count ei voitu palauttaa.',
            ],

            'restored_none' => [
                'title' => 'Palautus epäonnistui',
                'missing_authorization_failure_message' => 'Sinulla ei ole oikeuksia palauttaa :count.',
                'missing_processing_failure_message' => ':count ei voitu palauttaa.',
            ],

        ],

    ],

];
