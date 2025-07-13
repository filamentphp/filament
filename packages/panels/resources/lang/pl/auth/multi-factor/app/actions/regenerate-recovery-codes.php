<?php

return [

    'label' => 'Odśwież kody odzyskiwania',

    'modal' => [

        'heading' => 'Odśwież kody odzyskiwania',

        'description' => 'Jeśli zgubisz swoje kody odzyskiwania, możesz je tutaj odświeżyć. Twoje stare kody odzyskiwania zostaną natychmiast unieważnione.',

        'form' => [

            'code' => [

                'label' => 'Wprowadź 6-cyfrowy kod z aplikacji uwierzytelniającej',

                'validation_attribute' => 'kod',

                'messages' => [

                    'invalid' => 'Wprowadzony kod jest nieprawidłowy.',

                ],

            ],

            'password' => [

                'label' => 'Lub wprowadź swoje aktualne hasło',

                'validation_attribute' => 'hasło',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Odśwież kody odzyskiwania',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Nowe kody odzyskiwania zostały wygenerowane',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Nowe kody odzyskiwania',

            'description' => 'Zapisz poniższe kody odzyskiwania w bezpiecznym miejscu. Zostaną one wyświetlone tylko raz, ale będą potrzebne, jeśli stracisz dostęp do aplikacji uwierzytelniającej:',

            'actions' => [

                'submit' => [
                    'label' => 'Zamknij',
                ],

            ],

        ],

    ],

];
