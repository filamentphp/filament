<?php

return [

    'single' => [

        'label' => 'Löschen',

        'modal' => [

            'heading' => ':label löschen',

            'actions' => [

                'delete' => [
                    'label' => 'Löschen',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Gelöscht',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Ausgewählte löschen',

        'modal' => [

            'heading' => 'Ausgewählte :label löschen',

            'actions' => [

                'delete' => [
                    'label' => 'Ausgewählte löschen',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Gelöscht',
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
