<?php

return [

    'label' => 'Vypnout',

    'modal' => [

        'heading' => 'Vypnout e-mailové ověřovací kódy',

        'description' => 'Opravdu chcete přestat dostávat e-mailové ověřovací kódy? Vypnutím odstraníte další vrstvu zabezpečení Vašeho účtu.',

        'form' => [

            'code' => [

                'label' => 'Zadejte 6-místný kód, který jsme Vám poslali e-mailem',

                'validation_attribute' => 'kód',

                'actions' => [

                    'resend' => [

                        'label' => 'Poslat nový kód e-mailem',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Nový kód byl odeslán na Váš e-mail',
                            ],

                            'throttled' => [
                                'title' => 'Příliš mnoho pokusů o opětovné odeslání. Počkejte prosím, než požádáte o další kód.',
                            ],
                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Zadaný kód je neplatný.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Vypnout e-mailové ověřovací kódy',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'E-mailové ověřovací kódy byly vypnuty',
        ],

    ],

];
