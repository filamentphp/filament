<?php

return [

    'label' => 'Kikapcsolás',

    'modal' => [

        'heading' => 'E-mail ellenőrző kódok kikapcsolása',

        'description' => 'Biztosan ki szeretnéd kapcsolni az e-mail ellenőrző kódok küldését? Ennek letiltása eltávolít egy plusz biztonsági réteget a fiókodból.',

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
                'label' => 'E-mail ellenőrző kódok kikapcsolása',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Az e-mail ellenőrző kódok ki lettek kapcsolva',
        ],

    ],

];
