<?php

return [

    'label' => 'Profile',

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'name' => [
            'label' => 'Hming',
        ],

        'password' => [
            'label' => 'Password thar',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Password thar nemnghehna',
            'validation_attribute' => 'password confirmation',
        ],

        'current_password' => [
            'label' => 'Password hman mek',
            'below_content' => 'Security thil avangin hemi ti chhunzawm tur hian I password chhu rawh.',
            'validation_attribute' => 'current password',
        ],

        'actions' => [

            'save' => [
                'label' => 'Thlâkthlengna',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Two-factor authentication (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Email address thlak na thawn ani',
            'body' => 'Email thlak dilna na chu :email ah hian thawn ani. Khawngaihin hemi email atang hian a inthlak-ho verify rawh.',
        ],

        'saved' => [
            'title' => 'Saved',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Sûtna',
        ],

    ],

];
