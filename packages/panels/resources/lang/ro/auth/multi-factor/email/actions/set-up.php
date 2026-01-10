<?php

return [

    'label' => 'Configurează',

    'modal' => [

        'heading' => 'Configurare coduri de verificare prin email',

        'description' => 'Va trebui să introduceți codul din 6 cifre pe care vi-l trimitem prin email de fiecare dată când vă autentificați sau efectuați acțiuni sensibile. Verificați emailul pentru un cod din 6 cifre pentru a finaliza configurarea.',

        'form' => [

            'code' => [

                'label' => 'Introduceți codul din 6 cifre pe care vi l-am trimis prin email',

                'validation_attribute' => 'cod',

                'actions' => [

                    'resend' => [

                        'label' => 'Trimite un cod nou prin email',

                        'notifications' => [

                            'resent' => [
                                'title' => 'V-am trimis un cod nou prin email',
                            ],

                            'throttled' => [
                                'title' => 'Prea multe încercări de retrimitere. Vă rugăm să așteptați înainte de a solicita alt cod.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Codul introdus este invalid.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Activează codurile de verificare prin email',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Codurile de verificare prin email au fost activate',
        ],

    ],

];
