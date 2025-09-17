<?php

return [

    'label' => 'Aktiver',

    'modal' => [

        'heading' => 'Aktiver e‑postbekreftelseskoder for 2FA',

        'description' => 'Du må skrive inn den 6‑sifrede koden vi sender på e‑post hver gang du logger inn eller utfører sensitive handlinger. Sjekk e‑posten din for en 6‑sifret kode for å fullføre aktiveringen av 2FA.',

        'form' => [

            'code' => [

                'label' => 'Skriv inn den 6‑sifrede koden vi sendte på e‑post',

                'validation_attribute' => 'kode',

                'actions' => [

                    'resend' => [

                        'label' => 'Send en ny kode på e‑post',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Vi har sendt en ny kode på e‑post',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Koden du skrev inn er ugyldig.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Aktiver e‑postbekreftelseskoder',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'E‑postbekreftelseskoder er aktivert',
        ],

    ],

];
