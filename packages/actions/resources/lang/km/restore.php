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
                'title' => 'បានស្ដារឡើងវិញ :count នៃ :total',
                'missing_authorization_failure_message' => 'អ្នកមិនមានការអនុញ្ញាតឱ្យស្ដារ :count ទេ។',
                'missing_processing_failure_message' => ':count មិនអាចស្ដារឡើងវិញបានទេ។',
            ],

            'restored_none' => [
                'title' => 'បានបរាជ័យក្នុងការស្ដារឡើងវិញ',
                'missing_authorization_failure_message' => 'អ្នកមិនមានការអនុញ្ញាតឱ្យស្ដារ :count ទេ។',
                'missing_processing_failure_message' => ':count មិនអាចស្ដារឡើងវិញបានទេ។',
            ],

        ],

    ],

];
