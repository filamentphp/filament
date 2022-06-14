<?php

return [

    'breadcrumb' => 'Senarai',

    'actions' => [

        'create' => [

            'label' => 'Cipta :label',

            'modal' => [

                'heading' => 'Cipta :label',

                'actions' => [

                    'create' => [
                        'label' => 'Cipta',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Cipta & cipta yang lain',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'Diciptakan',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'Padam',

                'messages' => [
                    'deleted' => 'Dibuang',
                ],

            ],

            'edit' => [

                'label' => 'Sunting',

                'modal' => [

                    'heading' => 'Sunting :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Disimpan',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'Disimpan',
                ],

            ],

            'view' => [
                'label' => 'Lihat',
            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'Padam selected',

                'messages' => [
                    'deleted' => 'Dipadamkan',
                ],

            ],

        ],

    ],

];
