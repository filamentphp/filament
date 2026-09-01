<?php

return [

    'label' => 'Desativar',

    'modal' => [

        'heading' => 'Desativar códigos de verificação por e-mail',

        'description' => 'Tem certeza de que deseja parar de receber códigos de verificação por e-mail? Desativar isso removerá uma camada extra de segurança da sua conta.',

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
                'label' => 'Desativar códigos de verificação por e-mail',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Os códigos de verificação por e-mail foram desativados',
        ],

    ],

];
