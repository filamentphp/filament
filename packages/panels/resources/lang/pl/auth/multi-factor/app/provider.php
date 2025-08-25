<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Aplikacja uwierzytelniająca',

            'below_content' => 'Użyj bezpiecznej aplikacji, aby wygenerować tymczasowy kod do weryfikacji logowania.',

            'messages' => [
                'enabled' => 'Włączone',
                'disabled' => 'Wyłączone',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Użyj kodu z aplikacji uwierzytelniającej',

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

];
