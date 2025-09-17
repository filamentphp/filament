<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'App autenticador',

            'below_content' => 'Use um app seguro para gerar um código temporário para verificação de login.',

            'messages' => [
                'enabled' => 'Ativado',
                'disabled' => 'Desativado',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Usar um código do seu app autenticador',

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

];
