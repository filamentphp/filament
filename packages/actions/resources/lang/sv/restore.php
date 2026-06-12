<?php

return [

    'single' => [

        'label' => 'Återställ',

        'modal' => [

            'heading' => 'Återställ :label',

            'actions' => [

                'restore' => [
                    'label' => 'Återställ',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Återställdes',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Återställ valda',

        'modal' => [

            'heading' => 'Återställ valda :label',

            'actions' => [

                'restore' => [
                    'label' => 'Återställ',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Återställdes',
            ],

            'restored_partial' => [
                'title' => 'Återställde :count av :total',
                'missing_authorization_failure_message' => 'Du har inte behörighet att återställa :count.',
                'missing_processing_failure_message' => ':count kunde inte återställas.',
            ],

            'restored_none' => [
                'title' => 'Misslyckades att återställa',
                'missing_authorization_failure_message' => 'Du har inte behörighet att återställa :count.',
                'missing_processing_failure_message' => ':count kunde inte återställas.',
            ],

        ],

    ],

];
