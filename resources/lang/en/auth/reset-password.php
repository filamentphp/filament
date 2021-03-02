<?php

return [

    'buttons' => [

        'submit' => [
            'label' => 'Reset password',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email address',
        ],

        'password' => [
            'label' => 'Password',
        ],

        'passwordConfirmation' => [
            'label' => 'Password confirmation',
        ],

    ],

    'messages' => [

        'passwords' => [
            'throttled' => 'Please wait before retrying.',
            'token' => 'This password reset token is invalid.',
            'user' => 'We can\'t find a user with that email address.',
        ],

    ],

    'title' => 'Reset password',

];
