<?php

return [

    'label' => 'Kur',

    'modal' => [

        'heading' => 'E-posta doğrulama kodlarını kur',

        'description' => 'Her giriş yaptığınızda veya hassas işlemler gerçekleştirdiğinizde size e-posta ile gönderdiğimiz 6 haneli kodu girmeniz gerekecek. Kurulumu tamamlamak için e-postanızı kontrol edin ve 6 haneli kodu girin.',

        'form' => [

            'code' => [

                'label' => 'Size e-posta ile gönderdiğimiz 6 haneli kodu girin',

                'validation_attribute' => 'kod',

                'actions' => [

                    'resend' => [

                        'label' => 'E-posta ile yeni kod gönder',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Size e-posta ile yeni bir kod gönderdik',
                            ],

                            'throttled' => [
                                'title' => 'Çok fazla yeniden gönderme denemesi. Lütfen yeni bir kod istemeden önce bekleyin.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Girdiğiniz kod geçersiz.',

                    'rate_limited' => 'Çok fazla deneme. Lütfen daha sonra tekrar deneyin.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'E-posta doğrulama kodlarını etkinleştir',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'E-posta doğrulama kodları etkinleştirildi',
        ],

    ],

];
