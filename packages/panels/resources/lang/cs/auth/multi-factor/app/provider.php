<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Ověřovací aplikace',

            'below_content' => 'Použijte bezpečnou aplikaci pro generování dočasného kódu k ověření přihlášení.',

            'messages' => [
                'enabled' => 'Povoleno',
                'disabled' => 'Zakázáno',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Použijte kód z Vaší ověřovací aplikace',

        'code' => [

            'label' => 'Zadejte 6-místný kód z ověřovací aplikace',

            'validation_attribute' => 'kód',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Místo toho použijte obnovovací kód',
                ],

            ],

            'messages' => [

                'invalid' => 'Zadaný kód je neplatný.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Nebo zadejte obnovovací kód',

            'validation_attribute' => 'obnovovací kód',

            'messages' => [

                'invalid' => 'Zadaný obnovovací kód je neplatný.',

            ],

        ],

    ],

];
