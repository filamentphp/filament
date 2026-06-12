<?php

return [

    'label' => 'Постави',

    'modal' => [

        'heading' => 'Постави апликација за автентификација',

        'description' => <<<'BLADE'
            Ќе ви треба апликација како Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) за да го завршите овој процес.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Скенирај го овој QR код со вашата апликација за автентификација:',

                'alt' => 'QR код за скенирање со апликација за автентификација',

            ],

            'text_code' => [

                'instruction' => 'Или внесете го овој код рачно:',

                'messages' => [
                    'copied' => 'Копирано',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Ве молиме зачувајте ги следните кодови за обновување на безбедно место. Тие ќе бидат прикажани само еднаш, но ќе ви требаат ако изгубите пристап до вашата апликација за автентификација:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Внесете го 6-цифрениот код од апликацијата за автентификација',

                'validation_attribute' => 'код',

                'below_content' => 'Ќе треба да го внесете 6-цифрениот код од вашата апликација за автентификација секој пат кога ќе се најавите или извршите чувствителни акции.',

                'messages' => [

                    'invalid' => 'Кодот што го внесовте не е валиден.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Овозможи апликација за автентификација',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Апликацијата за автентификација е овозможена',
        ],

    ],

];
