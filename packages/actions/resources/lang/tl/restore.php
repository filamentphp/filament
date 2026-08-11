<?php

return [

    'single' => [

        'label' => 'I-restore',

        'modal' => [

            'heading' => 'I-restore ang :label',

            'actions' => [

                'restore' => [
                    'label' => 'I-restore',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Na-restore',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'I-restore ang napili',

        'modal' => [

            'heading' => 'I-restore ang napiling :label',

            'actions' => [

                'restore' => [
                    'label' => 'I-restore',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Na-restore',
            ],

            'restored_partial' => [
                'title' => 'Na-restore ang :count sa :total',
                'missing_authorization_failure_message' => 'Wala kang pahintulot na i-restore ang :count.',
                'missing_processing_failure_message' => 'Hindi ma-restore ang :count.',
            ],

            'restored_none' => [
                'title' => 'Hindi na-restore',
                'missing_authorization_failure_message' => 'Wala kang pahintulot na i-restore ang :count.',
                'missing_processing_failure_message' => 'Hindi ma-restore ang :count.',
            ],

        ],

    ],

];
