<?php

return [

    'label' => 'Поставка',

    'modal' => [

        'heading' => 'Поставка апликације за аутентификацију',

        'description' => <<<'BLADE'
            Неопходна је апликација за аутентификацију попут Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) да бисте наставили.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Скенирајте QR код помоћу апликације за аутентификацију:',

                'alt' => 'QR за аутентификацију',

            ],

            'text_code' => [

                'instruction' => 'Или ручно унесите овај код:',

                'messages' => [
                    'copied' => 'Копирано',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Чувајте ове кодове за опоравак на безбедном месту. Они ће бити приказани само једном, али ће бити неопходни ако изгубите приступ апликацији за аутентификацију:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Унесите код од 6 цифара из апликације за аутентификацију',

                'validation_attribute' => 'код',

                'below_content' => 'Потребно је унети код од 6 цифара из апликације за аутентификацију сваки пут кад се пријављујете или извршавате осетљиве акције.',

                'messages' => [

                    'invalid' => 'Код који сте унели није исправан.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Омогући апликацију за аутентификацију',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Апликације за аутентификацију је омогућена',
        ],

    ],

];
