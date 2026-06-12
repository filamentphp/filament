<?php

return [

    'single' => [

        'label' => 'បង្ខំ​លុប',

        'modal' => [

            'heading' => 'បង្ខំ​លុប :label',

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

        'label' => 'បង្ខំការលុបដែលបានជ្រើសរើស',

        'modal' => [

            'heading' => 'បង្ខំការលុបដែលបានជ្រើសរើស :label',

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
                'title' => 'Deleted :count of :total',
                'missing_authorization_failure_message' => 'You don\'t have permission to delete :count.',
                'missing_processing_failure_message' => ':count could not be deleted.',
            ],

            'deleted_none' => [
                'title' => 'Failed to delete',
                'missing_authorization_failure_message' => 'You don\'t have permission to delete :count.',
                'missing_processing_failure_message' => ':count could not be deleted.',
            ],
        ],

    ],

];
