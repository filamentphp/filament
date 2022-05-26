<?php

return [

    'breadcrumb' => 'Přehled',

    'actions' => [

        'create' => [

            'label' => 'Vytvořit :label',

            'modal' => [

                'heading' => 'Vytvořit :label',

                'actions' => [

                    'create' => [
                        'label' => 'Vytvořit',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Vytvořit & vytvořit další',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'Vytvořeno',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'Smazat',

                'modal' => [
                    'heading' => 'Smazat :label',
                ],

                'messages' => [
                    'deleted' => 'Smazáno',
                ],

            ],

            'edit' => [

                'label' => 'Upravit',

                'modal' => [

                    'heading' => 'Upravit :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Uložit',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'Uloženo',
                ],

            ],

            'view' => [

                'label' => 'Zobrazit',

                'modal' => [

                    'heading' => 'Zobrazit :label',

                    'actions' => [

                        'close' => [
                            'label' => 'Zavřít',
                        ],

                    ],

                ],

            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'Smazat vybrané',

                'messages' => [
                    'deleted' => 'Smazáno',
                ],

            ],

        ],

    ],

];
