<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'E-mail ellenőrző kódok',

            'below_content' => 'Kapj egy ideiglenes kódot az e-mail címedre, hogy igazold a személyazonosságodat bejelentkezés során.',

            'messages' => [
                'enabled' => 'Bekapcsolva',
                'disabled' => 'Kikpcsolva',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Kód küldése az e-mail címedre',

        'code' => [

            'label' => 'Add meg a 6 jegyű kódot, amit e-mailben küldtünk',

            'validation_attribute' => 'kód',

            'actions' => [

                'resend' => [

                    'label' => 'Új kód küldése e-mailben',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Küldtünk neked egy új kódot e-mailben',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'A megadott kód érvénytelen.',

            ],

        ],

    ],

];
