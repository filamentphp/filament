<?php

return [

    'single' => [

        'label' => 'Hapus selamanya',

        'modal' => [

            'heading' => 'Hapus selamanya :label',

            'actions' => [

                'delete' => [
                    'label' => 'Hapus',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Data berhasil dihapus',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Hapus selamanya data yang dipilih',

        'modal' => [

            'heading' => 'Hapus selamanya :label yang dipilih',

            'actions' => [

                'delete' => [
                    'label' => 'Hapus',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Data berhasil dihapus',
            ],

            'deleted_partial' => [
                'title' => 'Menghapus :count dari :total',
                'missing_authorization_failure_message' => 'Anda tidak mempunyai akses untuk menghapus :count.',
                'missing_processing_failure_message' => ':count tidak dapat dihapus.',
            ],

            'deleted_none' => [
                'title' => 'Failed to delete',
                'missing_authorization_failure_message' => 'Anda tidak mempunyai akses untuk menghapus :count.',
                'missing_processing_failure_message' => ':count tidak dapat dihapus.',
            ],

        ],

    ],

];
