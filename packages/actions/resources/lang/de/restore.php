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

            'restored_partial' => [
                'title' => ':count von :total wiederhergestellt',
                'missing_authorization_failure_message' => 'Sie sind nicht berechtigt, :count wiederherzustellen.',
                'missing_processing_failure_message' => ':count konnte nicht wiederhergestellt werden.',
            ],

            'restored_none' => [
                'title' => 'Wiederherstellen fehlgeschlagen',
                'missing_authorization_failure_message' => 'Sie sind nicht berechtigt, :count wiederherzustellen.',
                'missing_processing_failure_message' => ':count konnte nicht wiederhergestellt werden.',
            ],

        ],

    ],

];
