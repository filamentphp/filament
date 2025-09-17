<?php

return [
    'label' => 'Vypnout',

    'modal' => [
        'heading' => 'Vypnout ověřovací aplikaci',

        'description' => 'Opravdu chcete přestat používat ověřovací aplikaci? Vypnutím odstraníte další vrstvu zabezpečení Vašeho účtu.',

        'form' => [
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
