<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Подтверждение через email',

            'below_content' => 'Получите временный код на ваш email-адрес для подтверждения при входе.',

            'messages' => [
                'enabled' => 'Включено',
                'disabled' => 'Отключено',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Отправить код на ваш email',

        'code' => [

            'label' => 'Введите 6-значный код, который мы отправили вам по email',

            'validation_attribute' => 'код',

            'actions' => [

                'resend' => [

                    'label' => 'Отправить новый код по email',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Мы отправили вам новый код по email',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Введенный код неверен.',

            ],

        ],

    ],

];
