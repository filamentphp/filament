<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Aplicación de autenticación',

            'below_content' => 'Utilice una aplicación segura para generar un código temporal para verificar el inicio de sesión.',

            'messages' => [
                'enabled' => 'Habilitada',
                'disabled' => 'Deshabilitada',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Use un código de su aplicación de autenticación',

        'code' => [

            'label' => 'Ingrese el código de 6 dígitos de la aplicación de autenticación',

            'validation_attribute' => 'código',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Use un código de recuperación en su lugar',
                ],

            ],

            'messages' => [

                'invalid' => 'El código ingresado no es válido.',

            ],

        ],

        'recovery_code' => [

            'label' => 'O bien, ingrese un código de recuperación',

            'validation_attribute' => 'código de recuperación',

            'messages' => [

                'invalid' => 'El código de recuperación ingresado no es válido.',

            ],

        ],

    ],

];
