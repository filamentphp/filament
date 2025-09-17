<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Códigos de verificación por correo electrónico',

            'below_content' => 'Reciba un código temporal en su correo electrónico para verificar su identidad durante el inicio de sesión.',

            'messages' => [
                'enabled' => 'Habilitados',
                'disabled' => 'Deshabilitados',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Envíar un código a su correo electrónico',

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

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'El código ingresado no es válido.',

            ],

        ],

    ],

];
