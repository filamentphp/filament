<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Hitelesítő alkalmazás',

            'below_content' => 'Használj egy hitelesítő alkalmazást ideiglenes kód generálására a bejelentkezés ellenőrzéséhez.',

            'messages' => [
                'enabled' => 'Bekapcsolva',
                'disabled' => 'Kikapcsolva',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Használd a kódot a hitelesítő alkalmazásodból',

        'code' => [

            'label' => 'Add meg a 6 jegyű kódot a hitelesítő alkalmazásból',

            'validation_attribute' => 'kód',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Helyreállítási kód használata',
                ],

            ],

            'messages' => [

                'invalid' => 'A megadott kód érvénytelen.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Vagy add meg a helyreállítási kódot',

            'validation_attribute' => 'helyreállítási kód',

            'messages' => [

                'invalid' => 'A megadott helyreállítási kód érvénytelen.',

            ],

        ],

    ],

];
