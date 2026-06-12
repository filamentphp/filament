<?php

return [

    'label' => 'Configurar',

    'modal' => [

        'heading' => 'Configurar códigos de verificação por e-mail',

        'description' => 'Você precisará inserir o código de 6 dígitos que enviamos por e-mail sempre que fizer login ou realizar ações sensíveis. Verifique seu e-mail para obter um código de 6 dígitos e concluir a configuração.',

        'form' => [

            'code' => [

                'label' => 'Digite o código de 6 dígitos que enviamos por e-mail',

                'validation_attribute' => 'código',

                'actions' => [

                    'resend' => [

                        'label' => 'Enviar um novo código por e-mail',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Enviamos um novo código por e-mail',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'O código informado é inválido.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Ativar códigos de verificação por e-mail',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Os códigos de verificação por e-mail foram ativados',
        ],

    ],

];
