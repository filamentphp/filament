<?php

return [

    'breadcrumb' => 'Lijst',

    'actions' => [

        'create' => [

            'label' => ':Label aanmaken',

            'modal' => [

                'heading' => ':Label aanmaken',

                'actions' => [

                    'create' => [
                        'label' => 'Aanmaken',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Aanmaken & nog een aanmaken',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'Aangemaakt',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'Verwijderen',

                'messages' => [
                    'deleted' => 'Verwijderd',
                ],

            ],

            'edit' => [

                'label' => 'Bewerken',

                'modal' => [

                    'heading' => ':Label bewerken',

                    'actions' => [

                        'save' => [
                            'label' => 'Opslaan',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'Opgeslagen',
                ],

            ],

            'view' => [
                'label' => 'Bekijken',
            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'Verwijder geselecteerde',

                'messages' => [
                    'deleted' => 'Verwijderd',
                ],

            ],

        ],

    ],

];
