<?php

return [

    'single' => [

        'label' => 'Geri yükle',

        'modal' => [

            'heading' => ':label geri yükle',

            'actions' => [

                'restore' => [
                    'label' => 'Geri yükle',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Kayıt geri yüklendi',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Seçileni geri yükle',

        'modal' => [

            'heading' => ':label seçileni geri yükle',

            'actions' => [

                'restore' => [
                    'label' => 'Geri yükle',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Kayıtlar geri yüklendi',
            ],

            'restored_partial' => [
                'title' => ':total kayıttan :count kayıt geri yüklendi',
                'missing_authorization_failure_message' => ':count kayıt geri yüklemek için gereken izniniz yok.',
                'missing_processing_failure_message' => ':count geri yüklenemedi.',
            ],

            'restored_none' => [
                'title' => 'Kayıtlar geri yüklenemedi',
                'missing_authorization_failure_message' => ':count kayıt geri yüklemek için gereken izniniz yok.',
                'missing_processing_failure_message' => ':count geri yüklenemedi.',
            ],

        ],

    ],

];
