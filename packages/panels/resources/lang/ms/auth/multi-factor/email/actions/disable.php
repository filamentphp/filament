<?php

return [

    'label' => 'Nyahaktifkan',

    'modal' => [

        'heading' => 'Nyahaktifkan kod pengesahan emel',

        'description' => 'Adakah anda pasti ingin berhenti menerima kod pengesahan emel? Menyahaktifkan ini akan mengeluarkan lapisan keselamatan tambahan dari akaun anda.',

        'form' => [

            'code' => [

                'label' => 'Masukkan kod 6-digit yang kami hantar kepada anda melalui emel',

                'validation_attribute' => 'kod',

                'actions' => [

                    'resend' => [

                        'label' => 'Hantar kod baru melalui emel',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Kami telah menghantar kod baru kepada anda melalui emel',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Kod yang anda masukkan tidak sah.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Nyahaktifkan kod pengesahan emel',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Kod pengesahan emel telah dinyahaktifkan',
        ],

    ],

];
