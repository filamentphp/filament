<?php

return [

    'single' => [

        'label' => 'Thai bo hlenna',

        'modal' => [

            'heading' => ':Label thai bo hlenna',

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

        'label' => 'Thlanho thai bo hlenna',

        'modal' => [

            'heading' => ':Label thlanho thai bo hlenna',

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
