<?php

return [

    'label' => 'Wyłącz',

    'modal' => [

        'heading' => 'Wyłącz kody weryfikacji poprzez e-mail',

        'description' => 'Czy na pewno chcesz przestać otrzymywać kody weryfikacji poprzez e-mail? Wyłączenie tej funkcji usunie dodatkową warstwę zabezpieczeń z Twojego konta.',

        'form' => [

            'code' => [

                'label' => 'Wprowadź 6-cyfrowy kod, który został wysłany na Twój adres e-mail',

                'validation_attribute' => 'kod',

                'actions' => [

                    'resend' => [

                        'label' => 'Wyślij nowy kod w wiadomości e-mail',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Nowy kod został wysłany',
                            ],

                            'throttled' => [
                                'title' => 'Zbyt wiele prób ponownego wysłania. Proszę poczekać przed żądaniem kolejnego kodu.',
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
                'label' => 'Wyłącz kody weryfikacji poprzez e-mail',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Kody weryfikacji poprzez e-mail zostały wyłączone',
        ],

    ],

];
