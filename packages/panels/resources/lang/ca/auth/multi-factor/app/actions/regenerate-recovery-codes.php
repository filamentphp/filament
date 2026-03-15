<?php

return [

    'label' => 'Regenerar codis de recuperació',

    'modal' => [

        'heading' => 'Regenerar codis de recuperació de l\'aplicació d\'autenticació',

        'description' => 'Si perds els codis de recuperació, pots regenerar-los aquí. Els codis de recuperació antics s\'invalidaran immediatament.',

        'form' => [

            'code' => [

                'label' => 'Introdueix el codi de 6 dígits de l\'aplicació d\'autenticació',

                'validation_attribute' => 'codi',

                'messages' => [

                    'invalid' => 'El codi introduït no és vàlid.',

                    'rate_limited' => 'Massa intents. Si us plau, prova-ho més tard.',

                ],

            ],

            'password' => [

                'label' => 'O bé, introdueix la teva contrasenya actual',

                'validation_attribute' => 'contrasenya',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Regenerar codis de recuperació',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'S\'han generat nous codis de recuperació de l\'aplicació d\'autenticació',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Nous codis de recuperació',

            'description' => 'Guarda els codis de recuperació en un lloc segur. Només es mostraran una vegada. Els necessitaràs si perds el accés a la teva aplicació d\'autenticació:',

            'actions' => [

                'submit' => [
                    'label' => 'Tancar',
                ],

            ],

        ],

    ],

];
