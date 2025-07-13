<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Kody weryfikacji poprzez e-mail',

            'below_content' => 'Otrzymaj tymczasowy kod na swój adres e-mail, aby zweryfikować swoją tożsamość podczas logowania.',

            'messages' => [
                'enabled' => 'Włączone',
                'disabled' => 'Wyłączone',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Wyślij kod na swój adres e-mail',

        'code' => [

            'label' => 'Wprowadź 6-cyfrowy kod, który został wysłany na Twój adres e-mail',

            'validation_attribute' => 'kod',

            'actions' => [

                'resend' => [

                    'label' => 'Wyślij nowy kod w wiadomości e-mail',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Nowy kod został wysłany na Twój adres e-mail',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Wprowadzony kod jest nieprawidłowy.',

            ],

        ],

    ],

];
