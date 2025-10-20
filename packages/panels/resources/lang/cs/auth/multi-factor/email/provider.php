<?php

return [
    'management_schema' => [
        'actions' => [
            'label' => 'E-mailové ověřovací kódy',
            'below_content' => 'Při přihlášení obdržíte dočasný kód na svou e-mailovou adresu pro ověření Vaší identity.',
            'messages' => [
                'enabled' => 'Povoleno',
                'disabled' => 'Zakázáno',
            ],
        ],
    ],
    'login_form' => [
        'label' => 'Odeslat kód na Váš e-mail',
        'code' => [
            'label' => 'Zadejte 6-místný kód, který jsme Vám poslali e-mailem',
            'validation_attribute' => 'kód',
            'actions' => [
                'resend' => [
                    'label' => 'Odeslat nový kód e-mailem',
                    'notifications' => [
                        'resent' => [
                            'title' => 'Poslali jsme Vám nový kód e-mailem',
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
