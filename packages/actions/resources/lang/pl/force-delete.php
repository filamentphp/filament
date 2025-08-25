<?php

return [

    'single' => [

        'label' => 'Usuń trwale',

        'modal' => [

            'heading' => 'Trwale usuń :label',

            'actions' => [

                'delete' => [
                    'label' => 'Usuń trwale',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Trwale usunięto',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Trwale usuń zaznaczone',

        'modal' => [

            'heading' => 'Trwale usuń zaznaczone :label',

            'actions' => [

                'delete' => [
                    'label' => 'Usuń trwale',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Trwale usunięto',
            ],

            'deleted_partial' => [
                'title' => 'Usunięto :count z :total rekordów',
                'missing_authorization_failure_message' => 'Nie masz uprawnień do usunięcia :count rekordów.',
                'missing_processing_failure_message' => ':count rekordów nie mogło zostać usunięte.',
            ],

            'deleted_none' => [
                'title' => 'Usuwanie nie powiodło się',
                'missing_authorization_failure_message' => 'Nie masz uprawnień do usunięcia :count rekordów.',
                'missing_processing_failure_message' => ':count rekordów nie mogło zostać usunięte.',
            ],

        ],

    ],

];
