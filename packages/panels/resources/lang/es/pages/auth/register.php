
<?php

return [

    'title' => 'Registrarse',

    'heading' => 'Crear una cuenta',

    'buttons' => [

        'login' => [
            'before' => 'o',
            'label' => 'iniciar sesión en su cuenta',
        ],

        'register' => [
            'label' => 'Registrarse',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Correo electrónico',
        ],

        'name' => [
            'label' => 'Nombre',
        ],

        'password' => [
            'label' => 'Contraseña',
            'validation_attribute' => 'contraseña',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar contraseña',
        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Demasiados intentos de registro. Por favor, inténtelo de nuevo en :seconds segundos.',
        ],

    ],

];
