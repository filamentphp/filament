<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'E-posta doğrulama kodları',

            'below_content' => 'Giriş sırasında kimliğinizi doğrulamak için e-posta adresinize geçici bir kod alın.',

            'messages' => [
                'enabled' => 'Etkin',
                'disabled' => 'Devre dışı',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'E-postanıza kod gönder',

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

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Girdiğiniz kod geçersiz.',

            ],

        ],

    ],

];
