<?php

return [

    'label' => 'Профиль',

    'form' => [

        'email' => [
            'label' => 'Адрес электронной почты',
        ],

        'name' => [
            'label' => 'Имя',
        ],

        'password' => [
            'label' => 'Новый пароль',
            'validation_attribute' => 'пароль',
        ],

        'password_confirmation' => [
            'label' => 'Подтвердите новый пароль',
            'validation_attribute' => 'подтверждение пароля',
        ],

        'current_password' => [
            'label' => 'Текущий пароль',
            'below_content' => 'В целях безопасности подтвердите ваш пароль, чтобы продолжить.',
            'validation_attribute' => 'текущий пароль',
        ],

        'actions' => [

            'save' => [
                'label' => 'Сохранить изменения',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Двухфакторная аутентификация (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Отправлен запрос на изменение email-адреса',
            'body' => 'Запрос на изменение вашего email-адреса отправлен на :email. Проверьте вашу почту для подтверждения изменения.',
        ],

        'saved' => [
            'title' => 'Сохранено',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'назад',
        ],

    ],

];
