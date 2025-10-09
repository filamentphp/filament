<?php

return [

    'single' => [

        'label' => 'Radera',

        'modal' => [

            'heading' => 'Radera :label',

            'actions' => [

                'delete' => [
                    'label' => 'Radera',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Raderades',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Radera valda',

        'modal' => [

            'heading' => 'Radera valda :label',

            'actions' => [

                'delete' => [
                    'label' => 'Radera',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Raderades',
            ],

            'deleted_partial' => [
                'title' => 'Raderade :count av :total',
                'missing_authorization_failure_message' => 'Du har inte behörighet att radera :count.',
                'missing_processing_failure_message' => ':count kunde inte raderas.',
            ],

            'deleted_none' => [
                'title' => 'Misslyckades att radera',
                'missing_authorization_failure_message' => 'Du har inte behörighet att radera :count.',
                'missing_processing_failure_message' => ':count kunde inte raderas.',
            ],

        ],

    ],

];
