<?php

return [

    'label' => 'Slå av',

    'modal' => [

        'heading' => 'Deaktiver e-postbekreftelseskoder',

        'description' => 'Er du sikker på at du vil slutte å motta e-postbekreftelseskoder? Å deaktivere dette vil fjerne et ekstra sikkerhetslag fra kontoen din.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Deaktiver e-postbekreftelseskoder',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'E-postbekreftelseskoder er deaktivert',
        ],

    ],

];
