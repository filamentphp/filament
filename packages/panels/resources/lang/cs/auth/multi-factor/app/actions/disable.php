<?php

return [
    'label' => 'Vypnout',

    'modal' => [
        'heading' => 'Vypnout ověřovací aplikaci',

        'description' => 'Opravdu chcete přestat používat ověřovací aplikaci? Vypnutím odstraníte další vrstvu zabezpečení vašeho účtu.',

        'form' => [
            'code' => [
                'label' => 'Zadejte šestimístný kód z ověřovací aplikace',

                'validation_attribute' => 'kód',

                'actions' => [
                    'use_recovery_code' => [
                        'label' => 'Místo toho použijte obnovovací kód',
                    ],
                ],

                'messages' => [
                    'invalid' => 'Zadaný kód je neplatný.',

                    'rate_limited' => 'Příliš mnoho pokusů. Zkuste to znovu později.',
                ],
            ],

            'recovery_code' => [
                'label' => 'Nebo zadejte obnovovací kód',

                'validation_attribute' => 'obnovovací kód',

                'messages' => [
                    'invalid' => 'Zadaný obnovovací kód je neplatný.',

                    'rate_limited' => 'Příliš mnoho pokusů. Zkuste to znovu později.',
                ],
            ],
        ],

        'actions' => [
            'submit' => [
                'label' => 'Vypnout ověřovací aplikaci',
            ],
        ],
    ],

    'notifications' => [
        'disabled' => [
            'title' => 'Ověřovací aplikace byla vypnuta',
        ],
    ],
];
