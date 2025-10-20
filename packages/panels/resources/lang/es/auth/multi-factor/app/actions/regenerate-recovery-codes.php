<?php

return [

    'label' => 'Regenerar códigos de recuperación',

    'modal' => [

        'heading' => 'Regenerar códigos de recuperación de la aplicación de autenticación',

        'description' => 'Si pierde sus códigos de recuperación, puede regenerarlos aquí. Sus códigos de recuperación antiguos se invalidarán inmediatamente.',

        'form' => [

            'code' => [

                'label' => 'Ingrese el código de 6 dígitos de la aplicación de autenticación',

                'validation_attribute' => 'código',

                'messages' => [

                    'invalid' => 'El código ingresado no es válido.',

                ],

            ],

            'password' => [

                'label' => 'O bien, introduzca su contraseña actual',

                'validation_attribute' => 'contraseña',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Regenerar códigos de recuperación',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Se han generado nuevos códigos de recuperación de la aplicación de autenticación',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Nuevos códigos de recuperación',

            'description' => 'Guarde los siguientes códigos de recuperación en un lugar seguro. Solo se mostrarán una vez, y los necesitará si pierde el acceso a su aplicación de autenticación:',

            'actions' => [

                'submit' => [
                    'label' => 'Cerrar',
                ],

            ],

        ],

    ],

];
