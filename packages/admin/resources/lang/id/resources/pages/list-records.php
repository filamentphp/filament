<?php

return [

    'breadcrumb' => 'Daftar',

    'actions' => [

        'create' => [

            'label' => ':label baru',

            'modal' => [

                'heading' => 'Buat :label',

                'actions' => [

                    'create' => [
                        'label' => 'Buat',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Buat & Buat lainnya',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'Telah dibuat',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'Delete',

                'messages' => [
                    'deleted' => 'Deleted',
                ],

            ],

            'edit' => [

                'label' => 'Edit',

                'modal' => [

                    'heading' => 'Edit :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Save',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'Saved',
                ],

            ],

            'view' => [
                'label' => 'Lihat',
            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'Hapus yang dipilih ',

                'messages' => [
                    'deleted' => 'Hapus',
                ],

            ],

        ],

    ],

];
