<?php

return [

    'label' => 'Налаштувати',

    'modal' => [

        'heading' => 'Налаштування аутентифікатора',

        'description' => <<<'BLADE'
            Для завершення цього процесу вам знадобиться додаток, наприклад Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>).
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Відскануйте цей QR-код за допомогою аутентифікатора:',

                'alt' => 'QR-код для сканування в аутентифікаторі',

            ],

            'text_code' => [

                'instruction' => 'Або введіть цей код вручну:',

                'messages' => [
                    'copied' => 'Скопійовано',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Збережіть наведені нижче резервні коди в надійному місці. Їх буде показано лише один раз, але вони знадобляться, якщо ви втратите доступ до аутентифікатора:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Введіть 6-значний код з аутентифікатора',

                'validation_attribute' => 'код',

                'below_content' => 'Вам потрібно буде вводити 6-значний код з аутентифікатора щоразу при вході або виконанні чутливих дій.',

                'messages' => [

                    'invalid' => 'Введений вами код недійсний.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Увімкнути аутентифікатор',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Аутентифікатор увімкнено',
        ],

    ],

];
