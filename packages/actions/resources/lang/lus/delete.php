<?php

return [

    'single' => [

        'label' => 'Thai bona',

        'modal' => [

            'heading' => ':Label thai bona',

            'actions' => [

                'delete' => [
                    'label' => 'Thai bona',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Thai bo ani e.',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Thlanho thai bona',

        'modal' => [

            'heading' => ':Label thlanho thai bona',

            'actions' => [

                'delete' => [
                    'label' => 'Thai bona',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Thai bo ani e.',
            ],

            'deleted_partial' => [
                'title' => ':total atanga :count thai bo ani.',
                'missing_authorization_failure_message' => 'Hemi :count thai bo phalna I neilo.',
                'missing_processing_failure_message' => ':count hi a thai bo theihloh.',
            ],

            'deleted_none' => [
                'title' => 'Thai bo tumna a hlawhchham',
                'missing_authorization_failure_message' => 'Hemi :count thai bo phalna I neilo.',
                'missing_processing_failure_message' => ':count hi a thai bo theihloh.',
            ],

        ],

    ],

];
