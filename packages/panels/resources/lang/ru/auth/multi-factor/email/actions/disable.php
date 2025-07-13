<?php

return [

    'label' => 'Отключить',

    'modal' => [

        'heading' => 'Отключить подтверждение по email',

        'description' => 'Вы уверены, что хотите прекратить получать коды подтверждения по email? Отключение удалит дополнительный уровень безопасности вашей учетной записи.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Отключить подтверждение по email',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Подтверждение по email отключено',
        ],

    ],

];
