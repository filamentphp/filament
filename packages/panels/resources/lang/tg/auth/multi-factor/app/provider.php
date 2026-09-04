<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Барномаи 2FA',

            'below_content' => 'Барномаи 2FA-ро барои тавлиди рамзи муваққатӣ ҷиҳати тасдиқи воридшавӣ истифода баред.',

            'messages' => [
                'enabled' => 'Фаъол',
                'disabled' => 'Ғайрифаъол',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Рамзро аз барномаи 2FA-и худ истифода баред',

        'code' => [

            'label' => 'Рамзи 6-рақамаро аз барномаи 2FA ворид кунед',

            'validation_attribute' => 'рамз',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Истифодаи рамзи барқарорсозӣ',
                ],

            ],

            'messages' => [

                'invalid' => 'Рамзи воридшуда нодуруст аст.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Ё рамзи барқарорсозиро ворид кунед',

            'validation_attribute' => 'рамзи барқарорсозӣ',

            'messages' => [

                'invalid' => 'Рамзи барқарорсозии воридшуда нодуруст аст.',

            ],

        ],

    ],

];
