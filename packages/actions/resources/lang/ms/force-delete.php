<?php

return [

    'single' => [

        'label' => 'Padam paksa',

        'modal' => [

            'heading' => 'Padam paksa :label',

            'actions' => [

                'delete' => [
                    'label' => 'Padam',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Terpadam',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Padam paksa pilihan',

        'modal' => [

            'heading' => 'Padam paksa pilihan :label',

            'actions' => [

                'delete' => [
                    'label' => 'Padam',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Terpadam',
            ],

            'deleted_partial' => [
                'title' => 'Terpadam :count daripada :total',
                'missing_authorization_failure_message' => 'Anda tidak mempunyai kebenaran untuk memadam :count.',
                'missing_processing_failure_message' => ':count tidak dapat dipadamkan.',
            ],

            'deleted_none' => [
                'title' => 'Tiada yang dipadam',
                'missing_authorization_failure_message' => 'Anda tidak mempunyai kebenaran untuk memadam :count.',
                'missing_processing_failure_message' => ':count tidak dapat dipadamkan.',
            ],

        ],

    ],

];
