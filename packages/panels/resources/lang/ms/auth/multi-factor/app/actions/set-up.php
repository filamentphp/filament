<?php

return [

    'label' => 'Tetapkan',

    'modal' => [

        'heading' => 'Tetapkan aplikasi authenticator',

        'description' => <<<'BLADE'
            Anda memerlukan aplikasi seperti Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) untuk menyelesaikan proses ini.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Imbas kod QR ini dengan aplikasi authenticator anda:',

                'alt' => 'Kod QR untuk diimbas dengan aplikasi authenticator',

            ],

            'text_code' => [

                'instruction' => 'Atau masukkan kod ini secara manual:',

                'messages' => [
                    'copied' => 'Disalin',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Sila simpan kod pemulihan berikut di tempat yang selamat. Ia hanya akan ditunjukkan sekali, tetapi anda memerlukannya jika anda kehilangan akses kepada aplikasi authenticator anda:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Masukkan kod 6-digit dari aplikasi authenticator',

                'validation_attribute' => 'kod',

                'below_content' => 'Anda perlu memasukkan kod 6-digit dari aplikasi authenticator anda setiap kali anda log masuk atau melakukan tindakan sensitif.',

                'messages' => [

                    'invalid' => 'Kod yang anda masukkan tidak sah.',

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
