<?php

return [

    'label' => 'Nastavit',

    'modal' => [

        'heading' => 'Nastavit e-mailové ověřovací kódy',

        'description' => 'Při každém přihlášení nebo provádění citlivých akcí budete muset zadat šestimístný kód, který vám zašleme e-mailem. Zkontrolujte svůj e-mail a zadejte šestimístný kód pro dokončení nastavení.',

        'form' => [

            'code' => [

                'label' => 'Zadejte šestimístný kód, který jsme vám poslali e-mailem',

                'validation_attribute' => 'kód',

                'actions' => [

                    'resend' => [

                        'label' => 'Odeslat nový kód e-mailem',

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
                'label' => 'Povolit e-mailové ověřovací kódy',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'E-mailové ověřovací kódy byly povoleny',
        ],

    ],

];
