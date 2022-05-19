<?php

return [

    'breadcrumb' => 'Daftar',

    'actions' => [

        'create' => [

            'label' => ':Label baru',

            'modal' => [

                'heading' => 'Buat :label',

                'actions' => [

                    'create' => [
                        'label' => 'Buat',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Buat & buat lainnya',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'Data berhasil dibuat',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'Hapus',

                'modal' => [
                    'heading' => 'Hapus :label',
                ],

                'messages' => [
                    'deleted' => 'Data berhasil dihapus',
                ],

            ],

            'edit' => [

                'label' => 'Edit',

                'modal' => [

                    'heading' => 'Edit :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Simpan',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'Data berhasil disimpan',
                ],

            ],

            'view' => [

                'label' => 'Lihat',

                'modal' => [

                    'heading' => 'Lihat :label',

                    'actions' => [

                        'close' => [
                            'label' => 'Tutup',
                        ],

                    ],

                ],

            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'Hapus yang dipilih',

                'messages' => [
                    'deleted' => 'Data berhasil dihapus',
                ],

            ],

        ],

    ],

];
