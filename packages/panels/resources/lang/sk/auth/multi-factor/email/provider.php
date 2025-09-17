<?php

return [
    'management_schema' => [
        'actions' => [
            'label' => 'E-mailové overovacie kódy',
            'below_content' => 'Počas prihlásenia dostanete dočasný kód na svoju e-mailovú adresu na overenie Vašej identity.',
            'messages' => [
                'enabled' => 'Povolené',
                'disabled' => 'Zakázané',
            ],
        ],
    ],
    'login_form' => [
        'label' => 'Odoslať kód na Váš e-mail',
        'code' => [
            'label' => 'Zadajte 6-miestny kód, ktorý sme Vám poslali e-mailom',
            'validation_attribute' => 'kód',
            'actions' => [
                'resend' => [
                    'label' => 'Odoslať nový kód e-mailom',
                    'notifications' => [
                        'resent' => [
                            'title' => 'Poslali sme Vám nový kód e-mailom',
                        ],
                    ],
                ],
            ],
            'messages' => [
                'invalid' => 'Zadaný kód je neplatný.',
            ],
        ],
    ],
];
