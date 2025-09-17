<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Doğrulama uygulaması',

            'below_content' => 'Girişinizi doğrulamak için doğrulama uygulamanız tarafından oluşturulan kodları kullanın',

            'messages' => [
                'enabled' => 'Etkin',
                'disabled' => 'Devre Dışı',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Use a code from your authenticator app',

        'code' => [

            'label' => 'Girişinizi doğrulamak için doğrulama uygulamanız tarafından oluşturulan bir kod girin',

            'validation_attribute' => 'kod',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Kurtarma kodu kullan',
                ],

            ],

            'messages' => [

                'invalid' => 'Girmiş olduğunuz kod geçersiz.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Veya kurtarma kodu girin',

            'validation_attribute' => 'kurtarma kodu',

            'messages' => [

                'invalid' => 'Girmiş olduğunuz kurtarma kodu geçersiz.',

            ],

        ],

    ],

];
