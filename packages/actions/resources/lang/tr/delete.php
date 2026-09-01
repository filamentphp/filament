<?php

return [

    'single' => [

        'label' => 'Sil',

        'modal' => [

            'heading' => ':label Sil',

            'actions' => [

                'delete' => [
                    'label' => 'Sil',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Silindi',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Seçilenleri sil',

        'modal' => [

            'heading' => 'Seçilenleri sil', // When ':label' is used here, the meaning is distorted.

            'actions' => [

                'delete' => [
                    'label' => 'Sil',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Silindi',
            ],

            'deleted_partial' => [
                'title' => ':total kayıttan :count kayıt silindi',
                'missing_authorization_failure_message' => ':count kayıt silmek için gereken izniniz yok.',
                'missing_processing_failure_message' => ':count kayıt silinemedi.',
            ],

            'deleted_none' => [
                'title' => 'Kayıtlar silinemedi',
                'missing_authorization_failure_message' => ':count kayıt silmek için gereken izniniz yok.',
                'missing_processing_failure_message' => ':count kayıt silinemedi.',
            ],
        ],

    ],

];
