<?php

return [

    'label' => 'Aktifkan',

    'modal' => [

        'heading' => 'Aktifkan kode verifikasi email',

        'description' => 'Anda harus memasukkan kode 6 digit yang kami kirimkan melalui email setiap kali Anda masuk atau melakukan tindakan sensitif. Periksa email Anda untuk mendapatkan kode tersebut untuk menyelesaikan proses ini.',

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
                'label' => 'Aktifkan kode verifikasi email',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Kode verifikasi email telah diaktifkan',
        ],

    ],

];
