<?php

return [

    'single' => [

        'label' => 'Geforceerd verwijderen',

        'modal' => [

            'heading' => ':Label geforceerd verwijderen',

            'actions' => [

                'delete' => [
                    'label' => 'Verwijderen',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Verwijderd',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Geselecteerde geforceerd verwijderen',

        'modal' => [

            'heading' => 'Geselecteerde :label geforceerd verwijderen',

            'actions' => [

                'delete' => [
                    'label' => 'Verwijderen',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Verwijderd',
            ],

            'deleted_partial' => [
                'title' => ':count van :total verwijderd',
                'missing_authorization_failure_message' => 'Je hebt geen toestemming om :count te verwijderen.',
                'missing_processing_failure_message' => ':count kon niet worden verwijderd.',
            ],

            'deleted_none' => [
                'title' => 'Failed to delete',
                'missing_authorization_failure_message' => 'Je hebt geen toestemming om :count te verwijderen.',
                'missing_processing_failure_message' => ':count kon niet worden verwijderd.',
            ],

        ],

    ],

];
