<?php

return [

    'label' => 'Isključi',

    'modal' => [

        'heading' => 'Isključivanje kodove za verifikaciju e-poštom',

        'description' => 'Da li ste sigurni da želite da prestanete da dobijate verifikacione kodove e-poštom? Isključivanje će ukloniti dodatni nivo zaštite vašeg naloga.',

        'form' => [

            'code' => [

                'label' => 'Unesite kod od 6 cifara poslat na vašu e-poštu',

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

        'actions' => [

            'submit' => [
                'label' => 'Isključivanje kodove verifikacije e-poštom',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Slanje kodova verifikacije e-poštom je isključeno',
        ],

    ],

];
