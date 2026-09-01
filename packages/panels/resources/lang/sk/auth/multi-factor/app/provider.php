<?php

return [
    'management_schema' => [
        'actions' => [
            'label' => 'Overovacia aplikácia',
            'below_content' => 'Použite bezpečnú aplikáciu na generovanie dočasného kódu na overenie prihlásenia.',
            'messages' => [
                'enabled' => 'Povolené',
                'disabled' => 'Zakázané',
            ],
        ],
    ],

    'login_form' => [
        'label' => 'Použite kód zo svojej overovacej aplikácie',

        'code' => [
            'label' => 'Zadajte 6-miestny kód z overovacej aplikácie',

            'validation_attribute' => 'kód',

            'actions' => [
                'use_recovery_code' => [
                    'label' => 'Namiesto toho použite obnovovací kód',
                ],
            ],

            'messages' => [
                'invalid' => 'Zadaný kód je neplatný.',
            ],
        ],

        'recovery_code' => [
            'label' => 'Alebo zadajte obnovovací kód',

            'validation_attribute' => 'obnovovací kód',

            'messages' => [
                'invalid' => 'Zadaný obnovovací kód je neplatný.',
            ],
        ],
    ],
];
