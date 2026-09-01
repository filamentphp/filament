<?php

return [

    'title' => 'Log masuk',

    'heading' => 'Log masuk ke akaun anda',

    'actions' => [

        'register' => [
            'before' => 'atau',
            'label' => 'mendaftar akaun',
        ],

        'request_password_reset' => [
            'label' => 'Lupa kata laluan?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Emel',
        ],

        'password' => [
            'label' => 'Kata laluan',
        ],

        'remember' => [
            'label' => 'Ingat saya',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Log masuk',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Sahkan identiti anda',

        'subheading' => 'Untuk meneruskan log masuk, anda perlu mengesahkan identiti anda.',

        'form' => [

            'provider' => [
                'label' => 'Bagaimana anda ingin mengesahkan?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Sahkan identiti',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Bukti kelayakan ini tidak sepadan dengan rekod kami.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Terlalu banyak percubaan log masuk',
            'body' => 'Sila cuba lagi dalam masa :seconds saat.',
        ],

    ],

];
