<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Kode verifikasi email',

            'below_content' => 'Terima kode sementara melalui alamat email Anda untuk memverifikasi identitas Anda saat login.',

            'messages' => [
                'enabled' => 'Aktif',
                'disabled' => 'Nonaktif',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Kirim kode ke email Anda',

        'code' => [

            'label' => 'Masukkan kode 6 digit yang kami kirimkan melalui email',

            'validation_attribute' => 'kode',

            'actions' => [

                'resend' => [

                    'label' => 'Kirim ulang kode melalui email',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Kami telah mengirimkan kode baru ke email Anda',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Kode yang Anda masukkan tidak valid.',

            ],

        ],

    ],

];
