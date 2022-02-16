<?php

return [

    'breadcrumb' => 'Lista',

    'actions' => [

        'create' => [

            'label' => 'Utwórz :label',

            'modal' => [

                'heading' => 'Utwórz :label',

                'actions' => [

                    'create' => [
                        'label' => 'Utwórz',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Utwórz i dodaj kolejną',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'Created',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'Usuń',

                'messages' => [
                    'deleted' => 'Usunięto',
                ],

            ],

            'edit' => [

                'label' => 'Edytuj',

                'modal' => [

                    'heading' => 'Edycja :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Zapisz',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'Zapisano',
                ],

            ],

            'view' => [
                'label' => 'Podgląd',
            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'Usuń zaznaczone',

                'messages' => [
                    'deleted' => 'Usunięto',
                ],

            ],

        ],

    ],

];
