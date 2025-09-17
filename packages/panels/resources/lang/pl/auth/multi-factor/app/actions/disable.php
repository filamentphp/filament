<?php

return [

    'label' => 'Wyłącz',

    'modal' => [

        'heading' => 'Wyłącz aplikację uwierzytelniającą',

        'description' => 'Czy na pewno chcesz przestać używać aplikacji uwierzytelniającej? Wyłączenie jej usunie dodatkowe zabezpieczenie z Twojego konta.',

        'form' => [

            'code' => [

                'label' => 'Wprowadź 6-cyfrowy kod z aplikacji uwierzytelniającej',

                'validation_attribute' => 'kod',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Użyj kodu odzyskiwania',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Wprowadzony kod jest nieprawidłowy.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Lub wprowadź kod odzyskiwania',

                'validation_attribute' => 'kod odzyskiwania',

                'messages' => [

                    'invalid' => 'Wprowadzony kod odzyskiwania jest nieprawidłowy.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Wyłącz aplikację uwierzytelniającą',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Aplikacja uwierzytelniająca została wyłączona',
        ],

    ],

];
