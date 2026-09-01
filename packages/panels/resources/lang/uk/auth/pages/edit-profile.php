<?php

return [

    'label' => 'Профіль',

    'form' => [

        'email' => [
            'label' => 'Електронна пошта',
        ],

        'name' => [
            'label' => 'Ім\'я',
        ],

        'password' => [
            'label' => 'Новий пароль',
            'validation_attribute' => 'пароль',
        ],

        'password_confirmation' => [
            'label' => 'Введіть новий пароль ще раз',
            'validation_attribute' => 'підтвердження пароля',
        ],

        'current_password' => [
            'label' => 'Поточний пароль',
            'below_content' => 'З міркувань безпеки підтвердьте свій пароль, щоб продовжити.',
            'validation_attribute' => 'поточний пароль',
        ],

        'actions' => [

            'save' => [
                'label' => 'Зберегти зміни',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Двофакторна автентифікація (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Запит на зміну електронної адреси надіслано',
            'body' => 'Запит на зміну вашої електронної адреси надіслано на :email. Перевірте свою пошту для підтвердження зміни.',
        ],

        'saved' => [
            'title' => 'Збережено',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Відмінити',
        ],

    ],

];
