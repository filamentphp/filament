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

            'messages' => [
                'created' => 'Oluşturuldu',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'Sil',

                'modal' => [
                    'heading' => 'Sil :label',
                ],

                'messages' => [
                    'deleted' => 'Silindi',
                ],

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

                'messages' => [
                    'saved' => 'Kaydedildi',
                ],

            ],

            'view' => [

                'label' => 'Görüntüle',

                'modal' => [

                    'heading' => 'Görüntüle :label',

                    'actions' => [

                        'close' => [
                            'label' => 'Kapat',
                        ],

                    ],

                ],

            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'Seçiliyi sil',

                'messages' => [
                    'deleted' => 'Silindi',
                ],

            ],

        ],

    ],

];
