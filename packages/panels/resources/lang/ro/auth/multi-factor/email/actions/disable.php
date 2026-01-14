<?php

return [

    'label' => 'Dezactivează',

    'modal' => [

        'heading' => 'Dezactivare coduri de verificare prin email',

        'description' => 'Sigur doriți să nu mai primiți coduri de verificare prin email? Dezactivarea acestora va elimina un nivel suplimentar de securitate din contul dumneavoastră.',

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
                'label' => 'Dezactivează codurile de verificare prin email',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Codurile de verificare prin email au fost dezactivate',
        ],

    ],

];
