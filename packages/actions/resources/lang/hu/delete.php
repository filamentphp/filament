<?php

return [

    'single' => [

        'label' => 'Törlés',

        'modal' => [

            'heading' => ':label törlése',

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

        'label' => 'Kijelöltek törlése',

        'modal' => [

            'heading' => 'Kijelölt :label törlése',

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
                'missing_processing_failure_message' => ':count elem nem törölhető.',
            ],

            'deleted_none' => [
                'title' => 'Törlés sikertelen',
                'missing_authorization_failure_message' => 'Nincs jogosultságod :count elem törléséhez.',
                'missing_processing_failure_message' => ':count elem nem törölhető.',
            ],

        ],

    ],

];
