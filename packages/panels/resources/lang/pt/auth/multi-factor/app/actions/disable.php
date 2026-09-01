<?php

return [

    'label' => 'Desativar',

    'modal' => [

        'heading' => 'Desativar aplicação de autenticação',

        'description' => 'Tem a certeza de que deseja parar de usar a aplicação de autenticação? Desativar esta opção removerá uma camada extra de segurança da sua conta.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Desativar aplicação de autenticação',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Aplicação de autenticação foi desativada',
        ],

    ],

];
