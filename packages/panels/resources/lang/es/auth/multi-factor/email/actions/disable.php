<?php

return [

    'label' => 'Apagar',

    'modal' => [

        'heading' => 'Deshabilitar códigos de verificación por correo',

        'description' => '¿Seguro que desea dejar de recibir códigos de verificación por correo? Desactivar esta opción eliminará una capa adicional de seguridad de su cuenta.',

        'form' => [

            'code' => [

                'label' => 'Ingrese el código de 6 dígitos que te enviamos por correo electrónico',

                'validation_attribute' => 'código',

                'actions' => [

                    'resend' => [

                        'label' => 'Enviar un código nuevo por correo electrónico',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Le hemos enviado un código nuevo por correo electrónico.',
                            ],

                            'throttled' => [
                                'title' => 'Demasiados intentos de reenvío. Espere antes de solicitar otro código.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'El código ingresado no es válido.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Deshabilitar códigos de verificación por correo',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Los códigos de verificación por correo han sido deshabilitados',
        ],

    ],

];
