<?php

return [

    'breadcrumb' => 'Liste',

    'actions' => [

        'create' => [

            'label' => ':label Oluştur',

            'modal' => [

                'heading' => ':label Oluştur',

                'actions' => [

                    'create' => [
                        'label' => 'Oluştur',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Oluştur ve başka bir tane oluştur',
                    ],

                ],

            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [
                'label' => 'Sil',
            ],

            'edit' => [

                'label' => 'Düzenle',

                'modal' => [

                    'heading' => 'Düzenle: :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Kaydet',
                        ],

                    ],

                ],

            ],

            'view' => [
                'label' => 'Görüntüle',
            ],

        ],

        'bulk_actions' => [

            'delete' => [
                'label' => 'Seçiliyi sil',
            ],

        ],

    ],

];
