<?php

return [

    'single' => [

        'label' => 'Tving sletting',

        'modal' => [

            'heading' => 'Tving sletting av :label',

            'actions' => [

                'delete' => [
                    'label' => 'Slett',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Slettet',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Tving sletting av valgte',

        'modal' => [

            'heading' => 'Tving sletting av valgte :label',

            'actions' => [

                'delete' => [
                    'label' => 'Slett',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Slettet',
            ],

            'deleted_partial' => [
                'title' => 'Slettet :count av :total',
                'missing_authorization_failure_message' => 'Du har ikke tillatelse til å slette :count.',
                'missing_processing_failure_message' => ':count kunne ikke slettes.',
            ],

            'deleted_none' => [
                'title' => 'Kunne ikke slette',
                'missing_authorization_failure_message' => 'Du har ikke tillatelse til å slette :count.',
                'missing_processing_failure_message' => ':count kunne ikke slettes.',
            ],

        ],

    ],

];
