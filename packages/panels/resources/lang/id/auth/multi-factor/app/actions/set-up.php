<?php

return [

    'label' => 'Aktifkan',

    'modal' => [

        'heading' => 'Aktifkan aplikasi authenticator',

        'description' => <<<'BLADE'
            Anda memerlukan aplikasi seperti Google authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) untuk menyelesaikan proses ini.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Pindai kode QR berikut menggunakan aplikasi authenticator Anda:',

                'alt' => 'Kode QR untuk dipindai dengan aplikasi authenticator',

            ],

            'text_code' => [

                'instruction' => 'Atau masukkan kode berikut:',

                'messages' => [
                    'copied' => 'Berhasil disalin',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Harap simpan kode pemulihan berikut pada tempat yang aman. Kode ini hanya akan ditampilkan sekali, tetapi akan diperlukan jika Anda kehilangan akses ke aplikasi authenticator anda:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Masukkan kode 6 digit dari aplikasi authenticator',

                'validation_attribute' => 'kode',

                'below_content' => 'Anda harus memasukkan kode 6 digit dari aplikasi authenticator setiap kali masuk atau melakukan tindakan sensitif.',

                'messages' => [

                    'invalid' => 'Kode yang Anda masukkan tidak valid.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Aktifkan aplikasi authenticator',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Aplikasi authenticator telah diaktifkan',
        ],

    ],

];
