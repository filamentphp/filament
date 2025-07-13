<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => '2FA-приложение',

            'below_content' => 'Используйте 2FA-приложение для генерации временного кода для подтверждения входа.',

            'messages' => [
                'enabled' => 'Включено',
                'disabled' => 'Отключено',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Используйте код из вашего 2FA-приложения',

        'code' => [

            'label' => 'Введите 6-значный код из 2FA-приложения',

            'validation_attribute' => 'код',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Использовать код восстановления',
                ],

            ],

            'messages' => [

                'invalid' => 'веденный код неверен.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Или введите код восстановления',

            'validation_attribute' => 'код восстановления',

            'messages' => [

                'invalid' => 'Введенный код восстановления неверен.',

            ],

        ],

    ],

];
