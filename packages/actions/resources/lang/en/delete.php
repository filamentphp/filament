<?php

return [

    'single' => [

        'label' => 'Delete',

        'modal' => [

            'heading' => 'Delete :label',

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

        'label' => 'Delete selected',

        'modal' => [

            'heading' => 'Delete selected :label',

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
