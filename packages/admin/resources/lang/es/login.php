<?php

return [

    'title' => 'Login',

    'heading' => 'Entra a tu cuenta',

    'buttons' => [

        'submit' => [
            'label' => 'Entrar',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Email',
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
        'throttled' => 'Demasiados intentos. Prueba de nuevo en :seconds segundos.',
    ],

];
