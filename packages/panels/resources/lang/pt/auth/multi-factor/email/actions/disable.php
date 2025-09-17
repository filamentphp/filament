<?php

return [

    'label' => 'Desativar',

    'modal' => [

        'heading' => 'Desativar códigos de verificação por email',

        'description' => 'Tem a certeza de que deseja parar de receber códigos de verificação por email? Desativar esta opção removerá uma camada extra de segurança da sua conta.',

        'form' => [

            'code' => [

                'label' => 'Introduza o código de 6 dígitos que lhe enviámos por email',

                'validation_attribute' => 'código',

                'actions' => [

                    'resend' => [

                        'label' => 'Enviar um novo código por email',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Enviámos-lhe um novo código por email',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'O código que introduziu é inválido.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Desativar códigos de verificação por email',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Códigos de verificação por email foram desativados',
        ],

    ],

];
