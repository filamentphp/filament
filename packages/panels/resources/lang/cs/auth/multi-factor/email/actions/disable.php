<?php

return [

    'label' => 'Vypnout',

    'modal' => [

        'heading' => 'Vypnout e-mailové ověřovací kódy',

        'description' => 'Opravdu chcete přestat dostávat e-mailové ověřovací kódy? Vypnutím odstraníte další vrstvu zabezpečení vašeho účtu.',

        'form' => [

            'code' => [

                'label' => 'Zadejte šestimístný kód, který jsme vám poslali e-mailem',

                'validation_attribute' => 'kód',

                'actions' => [

                    'resend' => [

                        'label' => 'Poslat nový kód e-mailem',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Nový kód byl odeslán na váš e-mail',
                            ],

                            'throttled' => [
                                'title' => 'Příliš mnoho pokusů o opětovné odeslání. Počkejte prosím, než požádáte o další kód.',
                            ],
                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Zadaný kód je neplatný.',

                    'rate_limited' => 'Příliš mnoho pokusů. Zkuste to znovu později.',

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
