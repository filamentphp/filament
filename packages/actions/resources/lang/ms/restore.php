<?php

return [

    'single' => [

        'label' => 'Pulihkan',

        'modal' => [

            'heading' => 'Pulihkan :label',

            'actions' => [

                'restore' => [
                    'label' => 'Pulihkan',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Dipulihkan',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Pulihkan pilihan',

        'modal' => [

            'heading' => 'Pulihkan pilihan :label',

            'actions' => [

                'restore' => [
                    'label' => 'Pulihkan',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Dipulihkan',
            ],

            'restored_partial' => [
                'title' => 'Dipulihkan :count daripada :total',
                'missing_authorization_failure_message' => 'Anda tidak mempunyai kebenaran untuk memulihkan :count.',
                'missing_processing_failure_message' => ':count tidak dapat dipulihkan.',
            ],

            'restored_none' => [
                'title' => 'Tiada dipulihkan',
                'missing_authorization_failure_message' => 'Anda tidak mempunyai kebenaran untuk memulihkan :count.',
                'missing_processing_failure_message' => ':count tidak dapat dipulihkan.',
            ],

        ],

    ],

];
