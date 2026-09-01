<?php

return [

    'label' => 'Desativar',

    'modal' => [

        'heading' => 'Desativar app autenticador',

        'description' => 'Tem certeza de que deseja parar de usar o app autenticador? Desativar isso removerá uma camada extra de segurança da sua conta.',

        'form' => [

            'code' => [

                'label' => 'Digite o código de 6 dígitos do app autenticador',

                'validation_attribute' => 'código',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Usar um código de recuperação',
                    ],

                ],

                'messages' => [

                    'invalid' => 'O código informado é inválido.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Ou, digite um código de recuperação',

                'validation_attribute' => 'código de recuperação',

                'messages' => [

                    'invalid' => 'O código de recuperação informado é inválido.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Desativar app autenticador',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'App autenticador desativado',
        ],

    ],

];
