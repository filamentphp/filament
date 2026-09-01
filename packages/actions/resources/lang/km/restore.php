<?php

return [

    'single' => [

        'label' => 'ស្តារ',

        'modal' => [

            'heading' => 'ស្តារ :label',

            'actions' => [

                'restore' => [
                    'label' => 'ស្តារ',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'បានស្ដារឡើងវិញ',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'បានជ្រើសរើសឡើងវិញ',

        'modal' => [

            'heading' => 'បានជ្រើសរើសឡើងវិញ :label',

            'actions' => [

                'restore' => [
                    'label' => 'ស្តារ',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'បានស្ដារឡើងវិញ',
            ],

            'restored_partial' => [
                'title' => 'បានស្តារ :count នៃ :total',
                'missing_authorization_failure_message' => 'អ្នកមិនមានសិទ្ធិស្តារ :count ទេ។',
                'missing_processing_failure_message' => ':count មិនអាចស្តារបានទេ។',
            ],

            'restored_none' => [
                'title' => 'បរាជ័យក្នុងការស្តារ',
                'missing_authorization_failure_message' => 'អ្នកមិនមានសិទ្ធិស្តារ :count ទេ។',
                'missing_processing_failure_message' => ':count មិនអាចស្តារបានទេ។',
            ],

        ],

    ],

];
