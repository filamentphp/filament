<?php

return [

    'single' => [

        'label' => 'Wiederherstellen',

        'modal' => [

            'heading' => ':label wiederherstellen',

            'actions' => [

                'restore' => [
                    'label' => 'Wiederherstellen',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Eintrag wiederhergestellt',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Ausgewählte wiederherstellen',

        'modal' => [

            'heading' => 'Ausgewählte :label wiederherstellen',

            'actions' => [

                'restore' => [
                    'label' => 'Wiederherstellen',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Einträge wiederhergestellt',
            ],

        ],

    ],

];
