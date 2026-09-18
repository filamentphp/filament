<?php

return [

    'title' => 'Воридшавӣ',

    'heading' => 'Ба аккаунти худ ворид шавед',

    'actions' => [

        'register' => [
            'before' => 'ё',
            'label' => 'аккаунт эҷод кунед',
        ],

        'request_password_reset' => [
            'label' => 'Рамзи худро фаромӯш кардед?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Суроғаи почтаи электронӣ',
        ],

        'password' => [
            'label' => 'Рамз',
        ],

        'remember' => [
            'label' => 'Маро дар хотир нигоҳ дор',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Ворид шудан',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Шахсияти худро тасдиқ кунед',

        'subheading' => 'Барои идома додани воридшавӣ, шумо бояд шахсияти худро тасдиқ намоед.',

        'form' => [

            'provider' => [
                'label' => 'Мехоҳед чӣ гуна тасдиқ кунед?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Ворид шудан',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Номи корбар ё рамз нодуруст аст.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Кӯшишҳои воридшавӣ аз ҳад зиёд шуданд',
            'body' => 'Лутфан пас аз :seconds сония дубора кӯшиш кунед.',
        ],

    ],

];
