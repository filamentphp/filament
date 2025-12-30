<?php

return [

    'label' => 'Configurar',

    'modal' => [

        'heading' => 'Configurar códigos de verificación por correo electrónico',

        'description' => 'Usted necesitará ingresar el código de 6 dígitos que le enviamos por correo electrónico cada vez que inicie sesión o realice acciones sensibles. Revise su correo electrónico para encontrar el código de 6 dígitos y completar la configuración.',

        'form' => [

            'code' => [

                'label' => 'Ingrese el código de 6 dígitos que le enviamos por correo electrónico',

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
                'label' => 'Habilitar códigos de verificación por correo',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Se han habilitado los códigos de verificación por correo electrónico',
        ],

    ],

];
