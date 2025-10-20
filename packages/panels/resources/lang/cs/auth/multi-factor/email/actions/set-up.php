<?php

return [

    'label' => 'Nastavit',

    'modal' => [

        'heading' => 'Nastavit e-mailové ověřovací kódy',

        'description' => 'Při každém přihlášení nebo provádění citlivých akcí budete muset zadat 6-místný kód, který Vám zašleme e-mailem. Zkontrolujte svůj e-mail a zadejte 6-místný kód pro dokončení nastavení.',

        'form' => [

            'code' => [

                'label' => 'Zadejte 6-místný kód, který jsme Vám poslali e-mailem',

                'validation_attribute' => 'kód',

                'actions' => [

                    'resend' => [

                        'label' => 'Odeslat nový kód e-mailem',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Nový kód byl odeslán na Váš e-mail',
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
