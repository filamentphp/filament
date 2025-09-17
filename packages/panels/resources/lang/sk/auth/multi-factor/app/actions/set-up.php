<?php

return [

    'label' => 'Nastaviť',

    'modal' => [

        'heading' => 'Nastaviť overovaciu aplikáciu',

        'description' => <<<'BLADE'
            Na dokončenie tohto procesu budete potrebovať aplikáciu ako Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>).
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Naskenujte tento QR kód pomocou overovacej aplikácie:',

                'alt' => 'QR kód na naskenovanie overovacou aplikáciou',

            ],

            'text_code' => [

                'instruction' => 'Alebo zadajte tento kód ručne:',

                'messages' => [
                    'copied' => 'Skopírované',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Uložte si nasledujúce záložné kódy na bezpečné miesto. Budú zobrazené iba raz, ale budete ich potrebovať, ak stratíte prístup k overovacej aplikácii:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Zadajte 6-miestny kód z overovacej aplikácie',

                'validation_attribute' => 'kód',

                'below_content' => 'Pri každom prihlásení alebo vykonávaní citlivých akcií budete musieť zadať 6-miestny kód z overovacej aplikácie.',

                'messages' => [

                    'invalid' => 'Zadaný kód je neplatný.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Povoliť overovaciu aplikáciu',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Overovacia aplikácia bola povolená',
        ],

    ],

];
