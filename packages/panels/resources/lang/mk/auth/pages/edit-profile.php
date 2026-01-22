<?php

return [

    'label' => 'Профил',

    'form' => [

        'email' => [
            'label' => 'Е-пошта адреса',
        ],

        'name' => [
            'label' => 'Име',
        ],

        'password' => [
            'label' => 'Нова лозинка',
            'validation_attribute' => 'лозинка',
        ],

        'password_confirmation' => [
            'label' => 'Потврди нова лозинка',
            'validation_attribute' => 'потврда на лозинка',
        ],

        'current_password' => [
            'label' => 'Тековна лозинка',
            'below_content' => 'За безбедност, ве молиме потврдете ја вашата лозинка за да продолжите.',
            'validation_attribute' => 'тековна лозинка',
        ],

        'actions' => [

            'save' => [
                'label' => 'Зачувај промени',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Двофакторна автентификација (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Барање за промена на е-пошта адресата е испратено',
            'body' => 'Барање за промена на вашата е-пошта адреса е испратено на :email. Ве молиме проверете ја вашата е-пошта за да ја потврдите промената.',
        ],

        'saved' => [
            'title' => 'Зачувано',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Откажи',
        ],

    ],

];
