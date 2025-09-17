<?php

return [

    'label' => 'Включить',

    'modal' => [

        'heading' => 'Настройка 2FA-приложения',

        'description' => <<<'BLADE'
            Для завершения процесса вам понадобится приложение наподобие Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>).
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Отсканируйте этот QR-код с помощью 2FA-приложения:',

                'alt' => 'QR-код для сканирования 2FA-приложением',

            ],

            'text_code' => [

                'instruction' => 'Или введите этот код вручную:',

                'messages' => [
                    'copied' => 'Скопировано',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Пожалуйста, сохраните следующие коды восстановления в безопасном месте. Они будут показаны только один раз, но понадобятся вам, если вы потеряете доступ к 2FA-приложению:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Введите 6-значный код из 2FA-приложения',

                'validation_attribute' => 'код',

                'below_content' => 'Вам потребуется вводить 6-значный код из 2FA-приложения каждый раз при входе в систему или выполнении конфиденциальных действий.',

                'messages' => [

                    'invalid' => 'Введенный код неверен.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Включить 2FA-приложение',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => '2FA-приложение включено',
        ],

    ],

];
