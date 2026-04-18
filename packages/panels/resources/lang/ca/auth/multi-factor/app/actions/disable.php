<?php

return [

    'label' => 'Desactivar',

    'modal' => [

        'heading' => 'Desactivar l\'aplicació d\'autenticació',

        'description' => 'Estàs segur que vols deixar de fer servir l\'aplicació d\'autenticació? Desactivar-la eliminarà una capa addicional de seguretat del teu compte.',

        'form' => [

            'code' => [

                'label' => 'Introdueix el codi de 6 dígits de l\'aplicació d\'autenticació',

                'validation_attribute' => 'codi',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Fes servir un codi de recuperació en el seu lloc',
                    ],

                ],

                'messages' => [

                    'invalid' => 'El codi introduït no és vàlid.',

                    'rate_limited' => 'Massa intents. Si us plau, torneu-ho a intentar més tard.',

                ],

            ],

            'recovery_code' => [

                'label' => 'O bé, introdueix un codi de recuperació',

                'validation_attribute' => 'codi de recuperació',

                'messages' => [

                    'invalid' => 'El codi de recuperació introduït no és vàlid.',

                    'rate_limited' => 'Massa intents. Si us plau, torneu-ho a intentar més tard.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Desactivar l\'aplicació d\'autenticació',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'L\'aplicació d\'autenticació ha estat desactivada',
        ],

    ],

];
