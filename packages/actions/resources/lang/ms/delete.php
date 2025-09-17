<?php

return [

    'single' => [

        'label' => 'Padam',

        'modal' => [

            'heading' => 'Padam :label',

            'actions' => [

                'delete' => [
                    'label' => 'Padam',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Dipadamkan',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Padam pilihan',

        'modal' => [

            'heading' => 'Padam pilihan :label',

            'actions' => [

                'delete' => [
                    'label' => 'Padam pilihan',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Dipadamkan',
            ],

            'deleted_partial' => [
                'title' => 'Dipadamkan :count daripada :total',
                'missing_authorization_failure_message' => 'Anda tidak mempunyai kebenaran untuk memadam :count.',
                'missing_processing_failure_message' => ':count tidak dapat dipadamkan.',
            ],

            'deleted_none' => [
                'title' => 'Tiada yang dipadamkan',
                'missing_authorization_failure_message' => 'Anda tidak mempunyai kebenaran untuk memadam :count.',
                'missing_processing_failure_message' => ':count tidak dapat dipadamkan.',
            ],

        ],

    ],

];
