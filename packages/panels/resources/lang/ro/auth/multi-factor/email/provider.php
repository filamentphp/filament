<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Coduri de verificare prin email',

            'below_content' => 'Primiți un cod temporar la adresa dumneavoastră de email pentru a vă verifica identitatea la autentificare.',

            'messages' => [
                'enabled' => 'Activat',
                'disabled' => 'Dezactivat',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Trimite un cod pe email',

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

];
