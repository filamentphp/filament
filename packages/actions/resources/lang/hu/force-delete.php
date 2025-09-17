<?php

return [

    'single' => [

        'label' => 'Végleges törlés',

        'modal' => [

            'heading' => ':label végleges törlése',

            'actions' => [

                'delete' => [
                    'label' => 'Törlés',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Törölve',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Kijelöltek végleges törlése',

        'modal' => [

            'heading' => 'Kijelölt :label végleges törlése',

            'actions' => [

                'delete' => [
                    'label' => 'Törlés',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Törölve',
            ],

            'deleted_partial' => [
                'title' => ':count elem törölve a :total-ból',
                'missing_authorization_failure_message' => 'Nincs jogosultságod :count elem törléséhez.',
                'missing_processing_failure_message' => ':count elem törlése nem sikerült.',
            ],

            'deleted_none' => [
                'title' => 'Törlés sikertelen',
                'missing_authorization_failure_message' => 'Nincs jogosultságod :count elem törléséhez.',
                'missing_processing_failure_message' => ':count elem törlése nem sikerült.',
            ],

        ],

    ],

];
