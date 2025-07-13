<?php

return [

    'label' => 'Skonfiguruj',

    'modal' => [

        'heading' => 'Skonfiguruj kody weryfikacji poprzez e-mail',

        'description' => 'Zostaniesz poproszony o 6-cyfrowy kod, który zostanie wysłany na Twój adres e-mail podczas logowania lub wykonywania wrażliwych czynności. Sprawdź swoją skrzynkę pocztową, aby znaleźć 6-cyfrowy kod potrzebny do zakończenia konfiguracji.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Włącz kody weryfikacji poprzez e-mail',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Kody weryfikacji poprzez e-mail zostały włączone',
        ],

    ],

];
