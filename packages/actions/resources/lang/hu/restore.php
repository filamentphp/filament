<?php

return [

    'single' => [

        'label' => 'Visszaállítás',

        'modal' => [

            'heading' => ':label visszaállítása',

            'actions' => [

                'restore' => [
                    'label' => 'Visszaállítás',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Visszaállítva',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Kijelöltek visszaállítása',

        'modal' => [

            'heading' => 'Kijelölt :label visszaállítása',

            'actions' => [

                'restore' => [
                    'label' => 'Visszaállítás',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Visszaállítva',
            ],

            'restored_partial' => [
                'title' => ':count elem visszaállítva :total-ból',
                'missing_authorization_failure_message' => 'Nincs jogosultságod :count elem visszaállítására.',
                'missing_processing_failure_message' => ':count elem nem állítható vissza.',
            ],

            'restored_none' => [
                'title' => 'Visszaállítás sikertelen',
                'missing_authorization_failure_message' => 'Nincs jogosultságod :count elem visszaállítására.',
                'missing_processing_failure_message' => ':count elem nem állítható vissza.',
            ],

        ],

    ],

];
