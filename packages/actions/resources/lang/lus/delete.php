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

        'label' => 'Thlanho delete na',

        'modal' => [

            'heading' => ':Label thlanho delete na',

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
                'missing_authorization_failure_message' => ':count delete phalna I neilo.',
                'missing_processing_failure_message' => ':count hi a delete theihloh.',
            ],

            'deleted_none' => [
                'title' => 'Failed to delete',
                'missing_authorization_failure_message' => ':count delete phalna I neilo.',
                'missing_processing_failure_message' => ':count hi a delete theihloh.',
            ],

        ],

    ],

];
