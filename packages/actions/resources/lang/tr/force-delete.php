<?php

return [

    'single' => [

        'label' => 'Kalıcı olarak sil',

        'modal' => [

            'heading' => ':label kalıcı olarak sil',

            'actions' => [

                'delete' => [
                    'label' => 'Kalıcı olarak sil',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Kayıt kalıcı olarak silindi',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Seçiliyi kalıcı olarak sil',

        'modal' => [

            'heading' => ':label seçiliyi kalıcı olarak sil',

            'actions' => [

                'delete' => [
                    'label' => 'Kalıcı olarak sil',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Kayıtlar kalıcı olarak silindi',
            ],

            'deleted_partial' => [
                'title' => ':total kayıttan :count kayıt kalıcı olarak silindi',
                'missing_authorization_failure_message' => ':count kayıtı kalıcı olarak silmek için gereken izniniz yok.',
                'missing_processing_failure_message' => ':count kayıt kalıcı olarak silinemedi.',
            ],

            'deleted_none' => [
                'title' => 'Kayıtlar kalıcı olarak silinemedi',
                'missing_authorization_failure_message' => ':count kayıtı kalıcı olarak silmek için gereken izniniz yok.',
                'missing_processing_failure_message' => ':count kayıt kalıcı olarak silinemedi.',
            ],

        ],

    ],

];
