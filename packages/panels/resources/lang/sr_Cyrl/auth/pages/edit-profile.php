<?php

return [

    'label' => 'Профил',

    'form' => [

        'email' => [
            'label' => 'Адреса е-поште',
        ],

        'name' => [
            'label' => 'Име',
        ],

        'password' => [
            'label' => 'Нова лозинка',
            'validation_attribute' => 'лозинка',
        ],

        'password_confirmation' => [
            'label' => 'Потврдите нову лозинку',
            'validation_attribute' => 'потврда лозинке',
        ],

        'current_password' => [
            'label' => 'Тренутна лозинка',
            'below_content' => 'Због безбедности морате да потврдите лозинку за наставак.',
            'validation_attribute' => 'тренутна лозинка',
        ],

        'actions' => [

            'save' => [
                'label' => 'Сачувај измене',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Двострука аутентификација (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Захтев за измену адресе е-поште је послат',
            'body' => 'Захтев за измену адресе е-поште је послат на :email. Проверите своју е-пошту како би верификовали промену.',
        ],

        'saved' => [
            'title' => 'Сачувано',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Одустани',
        ],

    ],

];
