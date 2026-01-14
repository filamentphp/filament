<?php

return [

    'single' => [

        'label' => 'Restaurare',

        'modal' => [

            'heading' => 'Restaurare :label',

            'actions' => [

                'restore' => [
                    'label' => 'Restaurare',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Restaurat cu succes',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Restaurare înregistrările selectate',

        'modal' => [

            'heading' => 'Restaurare :label selectate',

            'actions' => [

                'restore' => [
                    'label' => 'Restaurare',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Restaurat cu succes',
            ],

            'restored_partial' => [
                'title' => 'S-au restaurat :count din :total',
                'missing_authorization_failure_message' => 'Nu aveți permisiunea de a restaura :count.',
                'missing_processing_failure_message' => ':count nu au putut fi restaurate.',
            ],

            'restored_none' => [
                'title' => 'Restaurarea a eșuat',
                'missing_authorization_failure_message' => 'Nu aveți permisiunea de a restaura :count.',
                'missing_processing_failure_message' => ':count nu au putut fi restaurate.',
            ],

        ],

    ],

];
