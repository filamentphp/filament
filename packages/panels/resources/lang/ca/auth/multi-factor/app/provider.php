<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Aplicació d\'autenticació',

            'below_content' => 'Fes servir una aplicació segura per generar un codi temporal per verificar l\'inici de sessió.',

            'messages' => [
                'enabled' => 'Activada',
                'disabled' => 'Desactivada',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Fes servir un codi de la teva aplicació d\'autenticació',

        'code' => [

            'label' => 'Introdueix el codi de 6 dígits de la teva aplicació d\'autenticació',

            'validation_attribute' => 'codi',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Fes servir un codi de recuperació en lloc',
                ],

            ],

            'messages' => [

                'invalid' => 'El codi introduït no és vàlid.',

            ],

        ],

        'recovery_code' => [

            'label' => 'O bé, introdueix un codi de recuperació',

            'validation_attribute' => 'codi de recuperació',

            'messages' => [

                'invalid' => 'El codi de recuperació introduït no és vàlid.',

            ],

        ],

    ],

];
