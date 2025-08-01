<?php

return [

    'label' => 'Regenerar códigos de recuperação',

    'modal' => [

        'heading' => 'Regenerar códigos de recuperação da aplicação de autenticação',

        'description' => 'Se perder os seus códigos de recuperação, pode regenerá-los aqui. Os seus códigos de recuperação antigos serão invalidados imediatamente.',

        'form' => [

            'code' => [

                'label' => 'Introduza o código de 6 dígitos da aplicação de autenticação',

                'validation_attribute' => 'código',

                'messages' => [

                    'invalid' => 'O código que introduziu é inválido.',

                ],

            ],

            'password' => [

                'label' => 'Ou, introduza a sua palavra-passe atual',

                'validation_attribute' => 'palavra-passe',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Regenerar códigos de recuperação',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Novos códigos de recuperação da aplicação de autenticação foram gerados',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Novos códigos de recuperação',

            'description' => 'Por favor, guarde os seguintes códigos de recuperação num local seguro. Serão mostrados apenas uma vez, mas precisará deles se perder o acesso à sua aplicação de autenticação:',

            'actions' => [

                'submit' => [
                    'label' => 'Fechar',
                ],

            ],

        ],

    ],

];
