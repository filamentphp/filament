<?php

return [

    'single' => [

        'label' => 'Restore',

        'modal' => [

            'heading' => 'Restore :label',

            'actions' => [

                'restore' => [
                    'label' => 'Restore',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Restored',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Restore selected',

        'modal' => [

            'heading' => 'Restore selected :label',

            'actions' => [

                'restore' => [
                    'label' => 'Restore',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Restored',
            ],

            'restored_partial' => [
                'title' => 'Restored :count of :total',
                'missing_authorization_failure_message' => 'You don\'t have permission to restore :count.',
                'missing_processing_failure_message' => ':count could not be restored.',
            ],

            'restored_none' => [
                'title' => 'Failed to restore',
                'missing_authorization_failure_message' => 'You don\'t have permission to restore :count.',
                'missing_processing_failure_message' => ':count could not be restored.',
            ],

        ],

    ],

];
