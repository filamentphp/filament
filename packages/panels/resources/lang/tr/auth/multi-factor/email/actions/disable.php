<?php

return [

    'label' => 'Kapat',

    'modal' => [

        'heading' => 'E-posta doğrulama kodlarını devre dışı bırak',

        'description' => 'E-posta doğrulama kodları almayı durdurmak istediğinizden emin misiniz? Bu özelliği devre dışı bırakmak hesabınızdan ek bir güvenlik katmanını kaldıracaktır.',

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

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Girdiğiniz kod geçersiz.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'E-posta doğrulama kodlarını devre dışı bırak',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'E-posta doğrulama kodları devre dışı bırakıldı',
        ],

    ],

];
