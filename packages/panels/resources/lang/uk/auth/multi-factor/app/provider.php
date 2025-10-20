<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Аутентифікатор',

            'below_content' => 'Використовуйте захищений додаток для створення тимчасового коду підтвердження входу.',

            'messages' => [
                'enabled' => 'Увімкнено',
                'disabled' => 'Вимкнено',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Використайте код з вашого аутентифікатора',

        'code' => [

            'label' => 'Введіть 6-значний код з аутентифікатора',

            'validation_attribute' => 'код',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Використати резервний код',
                ],

            ],

            'messages' => [

                'invalid' => 'Введений вами код недійсний.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Або введіть резервний код',

            'validation_attribute' => 'резервний код',

            'messages' => [

                'invalid' => 'Введений вами резервний код недійсний.',

            ],

        ],

    ],

];
