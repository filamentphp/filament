<?php

return [

    'label' => 'Kikapcsolás',

    'modal' => [

        'heading' => 'Hitelesítő alkalmazás kikapcsolása',

        'description' => 'Biztosan ki szeretnéd kapcsolni a hitelesítő alkalmazás használatát? Ennek kikapcsolása eltávolít egy plusz biztonsági réteget a fiókodból.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Hitelesítő alkalmazás kikapcsolása',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'A hitelesítő alkalmazás ki lett kapcsolva',
        ],

    ],

];
