<?php

return [

    'single' => [

        'label' => 'Kustuta',

        'modal' => [

            'heading' => 'Kustuta :label',

            'actions' => [

                'delete' => [
                    'label' => 'Kustuta',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Kustutatud',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Kustuta valitud',

        'modal' => [

            'heading' => 'Kustuta valitud :label',

            'actions' => [

                'delete' => [
                    'label' => 'Kustuta',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Kustutatud',
            ],

            'deleted_partial' => [
                'title' => 'Kustutatud :count :total-st',
                'missing_authorization_failure_message' => 'Ei ole õigust kustutada :count.',
                'missing_processing_failure_message' => ':count ei saanud kustutada.',
            ],

            'deleted_none' => [
                'title' => 'Kustutamine ebaõnnestus',
                'missing_authorization_failure_message' => 'Ei ole õigust kustutada :count.',
                'missing_processing_failure_message' => ':count ei saanud kustutada.',
            ],

        ],

    ],

];
