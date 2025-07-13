<?php

return [

    'label' => 'Nonaktifkan',

    'modal' => [

        'heading' => 'Nonaktifkan kode verifikasi email',

        'description' => 'Apakah Anda yakin ingin berhenti menerima kode verifikasi melalui email? Menonaktifkan fitur ini akan mengurangi keamanan dari akun Anda.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Nonaktifkan kode verifikasi email',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Kode verifikasi email telah dinonaktifkan',
        ],

    ],

];
