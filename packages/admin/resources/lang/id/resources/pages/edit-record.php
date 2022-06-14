<?php

return [

    'title' => 'Edit :label',

    'breadcrumb' => 'Edit',

    'actions' => [

        'delete' => [

            'label' => 'Hapus',

            'modal' => [

                'heading' => 'Hapus :label',

                'subheading' => 'Apakah Anda yakin ingin melakukan ini?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Hapus',
                    ],

                ],

            ],

            'messages' => [
                'deleted' => 'Data berhasil dihapus',
            ],

        ],

        'view' => [
            'label' => 'Lihat',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'Batal',
            ],

            'save' => [
                'label' => 'Simpan',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Data berhasil disimpan',
    ],

];
