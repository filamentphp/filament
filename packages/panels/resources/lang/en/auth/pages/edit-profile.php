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
            'below_content' => 'For security, please confirm your password to continue.',
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
            'title' => 'Email address change request sent',
            'body' => 'A request to change your email address has been sent to :email. Please check your email to verify the change.',
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
