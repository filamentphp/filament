<?php

return [

    'buttons' => [

        'submit' => [
            'label' => 'Send password reset link',
        ],

    ],

    'form' => [

        'email' => [
            'hint' => 'Back to login',
            'label' => 'Email address',
        ],

    ],

    'messages' => [

        'throttled' => 'Too many login attempts. Please try again in :seconds seconds.',

        'passwords' => [
            'sent' => 'We have emailed your password reset link!',
            'throttled' => 'Please wait before retrying.',
            'user' => 'We can\'t find a user with that email address.',
        ],

    ],

    'title' => 'Reset password',

];
