<?php

return [

    'label' => 'Gerar novos códigos de recuperação',

    'modal' => [

        'heading' => 'Gerar novos códigos de recuperação do app autenticador',

        'description' => 'Se você perder seus códigos de recuperação, pode gerá-los novamente aqui. Seus códigos antigos serão invalidados imediatamente.',

        'form' => [

            'code' => [

                'label' => 'Digite o código de 6 dígitos do app autenticador',

                'validation_attribute' => 'código',

                'messages' => [

                    'invalid' => 'O código informado é inválido.',

                ],

            ],

            'password' => [

                'label' => 'Ou, digite sua senha atual',

                'validation_attribute' => 'senha',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Gerar novos códigos de recuperação',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Novos códigos de recuperação do app autenticador foram gerados',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Novos códigos de recuperação',

            'description' => 'Salve os seguintes códigos de recuperação em um local seguro. Eles serão exibidos apenas uma vez, mas você precisará deles se perder o acesso ao app autenticador:',

            'actions' => [

                'submit' => [
                    'label' => 'Fechar',
                ],

            ],

        ],

    ],

];
