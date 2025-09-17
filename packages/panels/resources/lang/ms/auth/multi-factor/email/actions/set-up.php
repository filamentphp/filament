<?php

return [

    'label' => 'Tetapkan',

    'modal' => [

        'heading' => 'Tetapkan kod pengesahan emel',

        'description' => 'Anda perlu memasukkan kod 6-digit yang kami hantar kepada anda melalui emel setiap kali anda log masuk atau melakukan tindakan sensitif. Semak emel anda untuk kod 6-digit bagi menyelesaikan tetapan ini.',

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
                'label' => 'Aktifkan kod pengesahan emel',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Kod pengesahan emel telah diaktifkan',
        ],

    ],

];
