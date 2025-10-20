<?php

return [

    'label' => 'Postavka',

    'modal' => [

        'heading' => 'Postavka verifikacionih kodova e-pošte',

        'description' => 'Moraćete da unosite kod od 6 cifara poslat na vašu e-poštu svaki put kad se prijavljujete ili izvršavate osetljive akcije. Proverite vašu e-poštu za kod od 6 cifara da biste završili postavku.',

        'form' => [

            'code' => [

                'label' => 'Unesite kod od 6 cifara poslat na vašu e-poštu',

                'validation_attribute' => 'kod',

                'actions' => [

                    'resend' => [

                        'label' => 'Pošaljite novi kod e-poštom',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Poslaćemo vam novi kod e-poštom',
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
                'label' => 'Omogućite kodove verifikacije e-poštom',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Kodovi verifikacije e-poštom su omogućeni',
        ],

    ],

];
