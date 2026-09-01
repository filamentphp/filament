<?php

return [

    'title' => 'Giriş Yap',

    'heading' => 'Oturum Aç',

    'actions' => [

        'register' => [
            'before' => 'veya',
            'label' => 'bir hesap oluşturun',
        ],

        'request_password_reset' => [
            'label' => 'Şifrenizi mi unuttunuz?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-posta adresi',
        ],

        'password' => [
            'label' => 'Şifre',
        ],

        'remember' => [
            'label' => 'Beni hatırla',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Giriş yap',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Kimliğinizi doğrulayın',

        'subheading' => 'Giriş yapmaya devam etmek için kimliğinizi doğrulamanız gerekmektedir.',

        'form' => [

            'provider' => [
                'label' => 'Nasıl doğrulamak istersiniz?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Girişi doğrula',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Bu kimlik bilgileri kayıtlarımızla eşleşmiyor.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Çok fazla giriş denemesi',
            'body' => 'Lütfen :seconds saniye sonra tekrar deneyin.',
        ],

    ],

];
