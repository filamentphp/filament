<?php

return [

    'single' => [

        'label' => 'Tvångsradera',

        'modal' => [

            'heading' => 'Tvångsradera :label',

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

        'label' => 'Tvångsradera valda',

        'modal' => [

            'heading' => 'Tvångsradera valda :label',

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
