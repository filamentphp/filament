<?php

return [

    'single' => [

        'label' => 'Pulihkan data',

        'modal' => [

            'heading' => 'Pulihkan :label',

            'actions' => [

                'restore' => [
                    'label' => 'Pulihkan',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Data berhasil dipulihkan',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Pulihkan data yang dipilih',

        'modal' => [

            'heading' => 'Pulihkan :label yang dipilih',

            'actions' => [

                'restore' => [
                    'label' => 'Pulihkan',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Data berhasil dipulihkan',
            ],

            'restored_partial' => [
                'title' => 'Pulihkan :count dari :total',
                'missing_authorization_failure_message' => 'Anda tidak punya akses untuk memulihkan :count.',
                'missing_processing_failure_message' => ':count tidak dapat dipulihkan.',
            ],

            'restored_none' => [
                'title' => 'Gagal memulihkan',
                'missing_authorization_failure_message' => 'Anda tidak punya akses untuk memulihkan :count.',
                'missing_processing_failure_message' => ':count tidak dapat dipulihkan.',
            ],

        ],

    ],

];
