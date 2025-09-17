<?php

return [

    'label' => 'Nastavit',

    'modal' => [

        'heading' => 'Nastavit ověřovací aplikaci',

        'description' => <<<'BLADE'
            K dokončení tohoto procesu budete potřebovat aplikaci, jako je Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>).
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Naskenujte tento QR kód pomocí ověřovací aplikace:',

                'alt' => 'QR kód ke skenování ověřovací aplikací',

            ],

            'text_code' => [

                'instruction' => 'Nebo zadejte tento kód ručně:',

                'messages' => [
                    'copied' => 'Zkopírováno',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Uložte si prosím následující záložní kódy na bezpečné místo. Budou zobrazeny pouze jednou, ale budete je potřebovat, pokud ztratíte přístup k ověřovací aplikaci:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Zadejte 6-místný kód z ověřovací aplikace',

                'validation_attribute' => 'code',

                'below_content' => 'Při každém přihlášení nebo provádění citlivých akcí budete muset zadat 6-místný kód z ověřovací aplikace.',

                'messages' => [

                    'invalid' => 'Zadaný kód je neplatný.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Povolit ověřovací aplikaci',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Ověřovací aplikace byla povolena',
        ],

    ],

];
