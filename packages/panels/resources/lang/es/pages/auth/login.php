<?php

return [

    'title' => 'Login',

    'heading' => 'Entre a su cuenta',

    'actions' => [

        'authenticate' => [
            'label' => 'Entrar',
        ],

    ],

    'form' => [

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
