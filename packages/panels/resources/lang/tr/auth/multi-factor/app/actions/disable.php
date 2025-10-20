<?php

return [

    'label' => 'Kapat',

    'modal' => [

        'heading' => 'Doğrulama uygulamasını devre dışı bırak',

        'description' => 'Doğrulama uygulamasını devre dışı bırakmak istediğinize emin misiniz? Bunu devre dışı bırakmak hesabınızda bulunan ekstra koruma katmanını kaldıracaktır.',

        'form' => [

            'code' => [

                'label' => 'Doğrulama uygulamanızdaki 6 haneli kodu girin',

                'validation_attribute' => 'kod',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Bunun yerine kurtarma kodu girin',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Girmiş olduğunuz kod geçersiz.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Veya, bir kurtarma kodu girin',

                'validation_attribute' => 'kurtarma kodu',

                'messages' => [

                    'invalid' => 'Girmiş olduğunuz kurtarma kodu geçersiz.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Uygulamayı devre dışı bırak',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Doğrulama uygulaması devre dışı bırakıldı',
        ],

    ],

];
