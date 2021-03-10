<?php

return [

    'buttons' => [

        'submit' => [
            'label' => 'Enviar enlace para restablecer contraseña',
        ],

    ],

    'form' => [

        'email' => [
            'hint' => 'Volver a inicio de sesión',
            'label' => 'Correo electrónico',
        ],

    ],

    'messages' => [

        'throttled' => 'Demasiados intentos de inicio de sesión. Vuelva a intentarlo en  :seconds segundos.',

        'passwords' => [
            'sent' => 'Te hemos enviado un correo con un enlace para restablecer tu contraseña!',
            'throttled' => 'Favor esperar antes de volver a intentarlo.',
            'user' => 'No podemos encontrar un usuario con esa dirección de correo electrónico.',
        ],

    ],

    'title' => 'Restablecer contraseña',

];
