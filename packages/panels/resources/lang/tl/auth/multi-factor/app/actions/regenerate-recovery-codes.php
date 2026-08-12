<?php

return [

    'label' => 'Gumawa ulit ng mga recovery code',

    'modal' => [

        'heading' => 'Gumawa ulit ng mga recovery code ng authenticator app',

        'description' => 'Kung mawala ang mga recovery code mo, puwede kang gumawa ulit dito. Mawawalan agad ng bisa ang mga lumang recovery code mo.',

        'form' => [

            'code' => [

                'label' => 'Ilagay ang 6-digit code mula sa authenticator app',

                'validation_attribute' => 'code',

                'messages' => [

                    'invalid' => 'Invalid ang code na inilagay mo.',

                    'rate_limited' => 'Masyadong maraming subok. Subukan ulit mamaya.',

                ],

            ],

            'password' => [

                'label' => 'O, ilagay ang kasalukuyan mong password',

                'validation_attribute' => 'password',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Gumawa ulit ng mga recovery code',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Nakagawa na ng mga bagong recovery code ng authenticator app',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Mga bagong recovery code',

            'description' => 'I-save ang mga sumusunod na recovery code sa ligtas na lugar. Isang beses lang ipapakita ang mga ito, pero kakailanganin mo ang mga ito kung mawalan ka ng access sa iyong authenticator app:',

            'actions' => [

                'submit' => [
                    'label' => 'Isara',
                ],

            ],

        ],

    ],

];
