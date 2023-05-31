<?php

return [

    'title' => 'Login',

    'heading' => 'Sign in',

    'buttons' => [

        'authenticate' => [
            'label' => 'Sign in',
        ],

        'register' => [
            'before' => 'or',
            'label' => 'sign up for an account',
        ],

        'request_password_reset' => [
            'label' => 'Forgotten your password?',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Email address',
        ],

        'password' => [
            'label' => 'Password',
        ],

        'remember' => [
            'label' => 'Remember me',
        ],

    ],

    'messages' => [
        'failed' => 'These credentials do not match our records.',
        'throttled' => 'Too many login attempts. Please try again in :seconds seconds.',
    ],

];
