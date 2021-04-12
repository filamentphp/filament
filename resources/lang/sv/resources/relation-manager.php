<?php

return [

    'buttons' => [

        'attach' => [
            'label' => 'Relatera existerande',
        ],

        'create' => [
            'label' => 'Relatera ny',
        ],

        'detach' => [
            'label' => 'Avrelatera',
        ],

    ],

    'modals' => [

        'attach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Ångra',
                ],

                'attach' => [
                    'label' => 'Relatera',
                ],

                'attachAnother' => [
                    'label' => 'Relatera & Relatera en till',
                ],

            ],

            'form' => [

                'related' => [
                    'placeholder' => 'Börja skriva för att söka...',
                ],

            ],

            'heading' => 'Relatera existerande',

            'messages' => [
                'attached' => 'Relaterad!',
            ],

        ],

        'create' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Ångra',
                ],

                'create' => [
                    'label' => 'Relatera',
                ],

                'createAnother' => [
                    'label' => 'Relatera & Relatera en till',
                ],

            ],

            'heading' => 'Ny',

            'messages' => [
                'created' => 'Relaterad!',
            ],

        ],

        'detach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Ångra',
                ],

                'detach' => [
                    'label' => 'Avrelatera',
                ],

            ],

            'description' => 'Är du säker på att du vill avrelatera valda rader? Det går inte att ångra.',

            'heading' => 'Vill du avrelatera valda rader? ',

            'messages' => [
                'detached' => 'Avrelaterad!',
            ],

        ],

        'edit' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Ångra',
                ],

                'save' => [
                    'label' => 'Spara',
                ],

            ],

            'heading' => 'Redigera',

            'messages' => [
                'saved' => 'Sparad!',
            ],

        ],

    ],

];
