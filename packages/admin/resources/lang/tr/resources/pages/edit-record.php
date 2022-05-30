<?php

return [

    'title' => 'Düzenle :label',

    'breadcrumb' => 'Düzenle',

    'actions' => [

        'delete' => [

            'label' => 'Sil',

            'modal' => [

                'heading' => ':label Sil',

                'subheading' => 'Bunu yapmak istediğinizden emin misiniz?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Sil',
                    ],

                ],

            ],

            'messages' => [
                'deleted' => 'Silindi',
            ],

        ],

        'view' => [
            'label' => 'Görüntüle',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'İptal',
            ],

            'save' => [
                'label' => 'Kaydet',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Kaydedildi',
    ],

];
