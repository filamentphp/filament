<?php

return [
    'management_schema' => [
        'actions' => [
            'label' => 'E-mailové ověřovací kódy',
            'below_content' => 'Při přihlášení obdržíte dočasný kód na svou e-mailovou adresu pro ověření vaší identity.',
            'messages' => [
                'enabled' => 'Povoleno',
                'disabled' => 'Zakázáno',
            ],
        ],
    ],
    'login_form' => [
        'label' => 'Odeslat kód na váš e-mail',
        'code' => [
            'label' => 'Zadejte šestimístný kód, který jsme vám poslali e-mailem',
            'validation_attribute' => 'kód',
            'actions' => [
                'resend' => [
                    'label' => 'Odeslat nový kód e-mailem',
                    'notifications' => [
                        'resent' => [
                            'title' => 'Poslali jsme vám nový kód e-mailem',
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
];
