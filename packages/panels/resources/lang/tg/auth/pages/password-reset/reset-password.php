<?php

return [

    'title' => 'Барқарор кардани рамз',

    'heading' => 'Барқарор кардани рамз',

    'form' => [

        'email' => [
            'label' => 'Суроғаи почтаи электронӣ',
        ],

        'password' => [
            'label' => 'Рамз',
            'validation_attribute' => 'рамз',
        ],

        'password_confirmation' => [
            'label' => 'Тасдиқи рамз',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Барқарор кардани рамз',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Кӯшишҳои барқароркунӣ аз ҳад зиёд шуданд',
            'body' => 'Лутфан пас аз :seconds сония дубора кӯшиш кунед.',
        ],

    ],

];
