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
            'restored' => 'Eintrag wiederhergestellt',
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
            'restored' => 'Einträge wiederhergestellt',
        ],

    ],

];
