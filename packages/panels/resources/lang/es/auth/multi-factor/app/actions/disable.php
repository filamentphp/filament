<?php

return [

    'label' => 'Apagar',

    'modal' => [

        'heading' => 'Deshabilitar la aplicación de autenticación',

        'description' => '¿Seguro que quiere dejar de usar la aplicación de autenticación? Deshabilitarla eliminará una capa adicional de seguridad de su cuenta.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Deshabilitar aplicación de autenticación',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'La aplicación de autenticación ha sido deshabilitada',
        ],

    ],

];
