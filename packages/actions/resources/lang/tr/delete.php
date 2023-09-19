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

        ],

    ],

];
