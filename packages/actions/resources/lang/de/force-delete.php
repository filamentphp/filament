<?php

return [

    'single' => [

        'label' => 'Endgültig löschen',

        'modal' => [

            'heading' => ':label endgültig löschen',

            'actions' => [

                'delete' => [
                    'label' => 'Löschen',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Eintrag gelöscht',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Ausgewählte endgültig löschen',

        'modal' => [

            'heading' => 'Ausgewählte :label endgültig löschen',

            'actions' => [

                'delete' => [
                    'label' => 'Löschen',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Einträge gelöscht',
            ],

            'deleted_partial' => [
                'title' => ':count von :total gelöscht',
                'missing_authorization_failure_message' => 'Sie sind nicht berechtigt, :count zu löschen.',
                'missing_processing_failure_message' => ':count konnte nicht gelöscht werden.',
            ],

            'deleted_none' => [
                'title' => 'Löschen fehlgeschlagen',
                'missing_authorization_failure_message' => 'Sie sind nicht berechtigt, :count zu löschen.',
                'missing_processing_failure_message' => ':count konnte nicht gelöscht werden.',
            ],

        ],

    ],

];
