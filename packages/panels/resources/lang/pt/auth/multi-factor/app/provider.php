<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Aplicação de autenticação',

            'below_content' => 'Use uma aplicação segura para gerar um código temporário para verificação de início de sessão.',

            'messages' => [
                'enabled' => 'Ativada',
                'disabled' => 'Desativada',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Use um código da sua aplicação de autenticação',

        'code' => [

            'label' => 'Introduza o código de 6 dígitos da aplicação de autenticação',

            'validation_attribute' => 'código',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Usar um código de recuperação',
                ],

            ],

            'messages' => [

                'invalid' => 'O código que introduziu é inválido.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Ou, introduza um código de recuperação',

            'validation_attribute' => 'código de recuperação',

            'messages' => [

                'invalid' => 'O código de recuperação que introduziu é inválido.',

            ],

        ],

    ],

];
