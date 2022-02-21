<?php

return [

    'title' => 'Sunting :label',

    'breadcrumb' => 'Sunting',

    'actions' => [

        'delete' => [

            'label' => 'Padam',

            'modal' => [

                'heading' => 'Padam :label',

                'subheading' => 'Adakah anda pasti mahu melakukan ini?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Padam',
                    ],

                ],

            ],

            'messages' => [
                'deleted' => 'Dipadamkan',
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
        'saved' => 'Disimpan',
    ],

];
