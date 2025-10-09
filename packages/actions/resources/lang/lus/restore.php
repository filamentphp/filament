<?php

return [

    'single' => [

        'label' => 'Ngai awhtîr lehna',

        'modal' => [

            'heading' => ':Label ngai awhtîr lehna',

            'actions' => [

                'restore' => [
                    'label' => 'Ngai awhtîr lehna',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Ngai awhtîr leh ani e.',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Thlanho ngai awhtîr lehna',

        'modal' => [

            'heading' => ':Label thlanho ngai awhtîr lehna',

            'actions' => [

                'restore' => [
                    'label' => 'Ngai awhtîr lehna',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Ngai awhtîr leh ani e.',
            ],

            'restored_partial' => [
                'title' => ':total atanga :count ngai awhtîr leh ani.',
                'missing_authorization_failure_message' => 'Hemi :count ngai awhtîr leh tur hian phalna I neilo.',
                'missing_processing_failure_message' => ':count hi ngai a awhtîr leh theihloh.',
            ],

            'restored_none' => [
                'title' => 'Failed to restore',
                'missing_authorization_failure_message' => 'Hemi :count ngai awhtîr leh tur hian phalna I neilo.',
                'missing_processing_failure_message' => ':count hi ngai a awhtîr leh theihloh.',
            ],

        ],

    ],

];
