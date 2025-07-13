<?php

return [

    'single' => [

        'label' => 'Usuń',

        'modal' => [

            'heading' => 'Usuń :label',

            'actions' => [

                'delete' => [
                    'label' => 'Usuń',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Usunięto',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Usuń zaznaczone',

        'modal' => [

            'heading' => 'Usuń zaznaczone :label',

            'actions' => [

                'delete' => [
                    'label' => 'Usuń zaznaczone',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Usunięto',
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
