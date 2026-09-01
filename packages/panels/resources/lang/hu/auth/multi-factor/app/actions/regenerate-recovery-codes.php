<?php

return [

    'label' => 'Helyreállítási kódok újragenerálása',

    'modal' => [

        'heading' => 'Hitelesítő alkalmazás helyreállítási kódjainak újragenerálása',

        'description' => 'Ha elveszted a helyreállítási kódjaidat, itt újragenerálhatod őket. A régi helyreállítási kódjaid azonnal érvénytelenítve lesznek.',

        'form' => [

            'code' => [

                'label' => 'Add meg a 6 jegyű kódot a hitelesítő alkalmazásból',

                'validation_attribute' => 'kód',

                'messages' => [

                    'invalid' => 'A megadott kód érvénytelen.',

                ],

            ],

            'password' => [

                'label' => 'Vagy add meg a jelenlegi jelszavadat',

                'validation_attribute' => 'jelszó',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Helyreállítási kódok újragenerálása',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Új helyreállítási kódok lettek generálva',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Új helyreállítási kódok',

            'description' => 'Kérjük, mentsd el a következő helyreállítási kódokat biztonságos helyre. Csak egyszer lesznek megjelenítve, de szükséged lesz rájuk, ha elveszted a hozzáférést a hitelesítő alkalmazásodhoz:',

            'actions' => [

                'submit' => [
                    'label' => 'Bezárás',
                ],

            ],

        ],

    ],

];
