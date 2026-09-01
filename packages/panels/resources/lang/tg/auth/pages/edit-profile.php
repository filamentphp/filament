<?php

return [

    'label' => 'Профил',

    'form' => [

        'email' => [
            'label' => 'Суроғаи почтаи электронӣ',
        ],

        'name' => [
            'label' => 'Ном',
        ],

        'password' => [
            'label' => 'Рамзи нав',
            'validation_attribute' => 'рамз',
        ],

        'password_confirmation' => [
            'label' => 'Тасдиқи рамзи нав',
            'validation_attribute' => 'тасдиқи рамз',
        ],

        'current_password' => [
            'label' => 'Рамзи ҷорӣ',
            'below_content' => 'Барои таъмини амният, лутфан рамзи худро тасдиқ кунед, то идома диҳед.',
            'validation_attribute' => 'рамзи ҷорӣ',
        ],

        'actions' => [

            'save' => [
                'label' => 'Нигоҳ доштани тағйирот',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Тасдиқи дуомила (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Дархости тағйир додани суроғаи почтаи электронӣ фиристода шуд',
            'body' => 'Дархост барои тағйир додани суроғаи почтаи электронии шумо ба :email фиристода шуд. Барои тасдиқи тағйирот почтаи электронии худро санҷед.',
        ],

        'saved' => [
            'title' => 'Нигоҳ дошта шуд',
        ],

        'throttled' => [
            'title' => 'Кӯшишҳо аз ҳад зиёд шуданд. Лутфан пас аз :seconds сония дубора кӯшиш кунед.',
            'body' => 'Лутфан пас аз :seconds сония дубора кӯшиш кунед.',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'бозгашт',
        ],

    ],

];
