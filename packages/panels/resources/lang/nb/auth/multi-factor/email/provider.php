<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'E-postbekreftelseskoder',

            'below_content' => 'Motta en midlertidig kode på e-postadressen din for å bekrefte identiteten din ved innlogging.',

            'messages' => [
                'enabled' => 'Aktivert',
                'disabled' => 'Deaktivert',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Send en kode til e-posten din',

        'code' => [

            'label' => 'Skriv inn 6-sifret kode vi sendte deg på e-post',

            'validation_attribute' => 'kode',

            'actions' => [

                'resend' => [

                    'label' => 'Send en ny kode på e-post',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Vi har sendt deg en ny kode på e-post',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Koden du skrev inn er ugyldig.',

            ],

        ],

    ],

];
