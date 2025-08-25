<?php

return [

    'label' => 'Profile',

    'form' => [

        'email' => [
            'label' => 'Email address',
        ],

        'name' => [
            'label' => 'Name',
        ],

        'password' => [
            'label' => 'New password',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Confirm new password',
            'validation_attribute' => 'password confirmation',
        ],

        'current_password' => [
            'label' => 'Current password',
            'below_content' => 'Security thil avangin, khawngaihin ti chhunzawm turin I password confirm rawh.',
            'validation_attribute' => 'current password',
        ],

        'actions' => [

            'save' => [
                'label' => 'Save changes',
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
            'label' => 'Cancel',
        ],

    ],

];
