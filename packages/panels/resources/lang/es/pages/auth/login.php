<?php

return [

    'title' => 'Login',

    'heading' => 'Entre a su cuenta',

    'buttons' => [

        'authenticate' => [
            'label' => 'Entrar',
        ],

        'register' => [
            'before' => 'o',
            'label' => 'registre una cuenta',
        ],

        'request_password_reset' => [
            'label' => '¿Olvidó su contraseña?',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Correo electrónico',
        ],

        'password' => [
            'label' => 'Contraseña',
        ],

        'remember' => [
            'label' => 'Recordarme',
        ],

    ],

    'messages' => [
        'failed' => 'Estas credenciales no coinciden con nuestros registros.',
        'throttled' => 'Demasiados intentos. Intente de nuevo en :seconds segundos.',
    ],

];
