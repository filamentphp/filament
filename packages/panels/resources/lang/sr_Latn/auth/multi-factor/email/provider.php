<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Kodovi za verifikaciju e-poštom',

            'below_content' => 'Primi privremeni kod na vašu adresu e-pošte da biste verifikovali identitet pri prijavi.',

            'messages' => [
                'enabled' => 'Uključeno',
                'disabled' => 'Isključeno',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Pošaljite kod na vašu adresu e-pošte',

        'code' => [

            'label' => 'Unesite kod od 6 cifara poslat na vašu adresu e-pošte',

            'validation_attribute' => 'kod',

            'actions' => [

                'resend' => [

                    'label' => 'Pošaljite novi kod e-poštom',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Poslali smo vam novi kod e-poštom',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Kod koji ste uneli nije ispravan.',

            ],

        ],

    ],

];
