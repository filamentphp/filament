<?php

return [

    'single' => [

        'label' => 'Force delete',

        'modal' => [

            'heading' => 'Force delete :label',

            'actions' => [

                'delete' => [
                    'label' => 'Delete',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Deleted',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Force delete selected',

        'modal' => [

            'heading' => 'Force delete selected :label',

            'actions' => [

                'delete' => [
                    'label' => 'Delete',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Deleted',
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
