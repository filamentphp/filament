<?php

return [

    'single' => [

        'label' => 'Przywróć',

        'modal' => [

            'heading' => 'Przywróć :label',

            'actions' => [

                'restore' => [
                    'label' => 'Przywróć',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Przywrócono',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Przywróć zaznaczone',

        'modal' => [

            'heading' => 'Przywróć zaznaczone :label',

            'actions' => [

                'restore' => [
                    'label' => 'Przywróć',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Przywrócono',
            ],

            'restored_partial' => [
                'title' => 'Przywrócono :count z :total rekordów',
                'missing_authorization_failure_message' => 'Nie masz uprawnień do przywrócenia :count rekordów.',
                'missing_processing_failure_message' => ':count rekordów nie mogło zostać przywróconych.',
            ],

            'restored_none' => [
                'title' => 'Przywracanie nie powiodło się',
                'missing_authorization_failure_message' => 'Nie masz uprawnień do przywrócenia :count rekordów.',
                'missing_processing_failure_message' => ':count rekordów nie mogło zostać przywróconych.',
            ],

        ],

    ],

];
