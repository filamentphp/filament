<?php

return [

    'label' => 'Bekapcsolás',

    'modal' => [

        'heading' => 'E-mail ellenőrző kódok bekapcsolása',

        'description' => 'Minden bejelentkezéskor vagy érzékeny művelet végrehajtásakor meg kell adnod a 6 jegyű kódot, amit e-mailben küldünk. Ellenőrizd az e-mailjeidet a 6 jegyű kódért a beállítás befejezéséhez.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'E-mail ellenőrző kódok bekapcsolása',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Az e-mail ellenőrző kódok be lettek kapcsolva',
        ],

    ],

];
