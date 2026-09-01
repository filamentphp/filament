<?php

return [

    'single' => [

        'label' => 'លុប',

        'modal' => [

            'heading' => 'លុប :label',

            'actions' => [

                'delete' => [
                    'label' => 'លុប',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'បានលុប',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'លុបចោលការជ្រើសរើស',

        'modal' => [

            'heading' => 'លុបចោលការជ្រើសរើស :label',

            'actions' => [

                'delete' => [
                    'label' => 'លុប',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'បានលុប',
            ],

            'deleted_partial' => [
                'title' => 'បានលុប :count នៃ :total',
                'missing_authorization_failure_message' => 'អ្នកមិនមានសិទ្ធិលុប :count ទេ។',
                'missing_processing_failure_message' => ':count មិនអាចលុបបានទេ។',
            ],

            'deleted_none' => [
                'title' => 'បរាជ័យក្នុងការលុប',
                'missing_authorization_failure_message' => 'អ្នកមិនមានសិទ្ធិលុប :count ទេ។',
                'missing_processing_failure_message' => ':count មិនអាចលុបបានទេ។',
            ],

        ],

    ],

];
