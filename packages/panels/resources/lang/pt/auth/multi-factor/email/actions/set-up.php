<?php

return [

    'label' => 'Configurar',

    'modal' => [

        'heading' => 'Configurar códigos de verificação por email',

        'description' => 'Precisará de introduzir o código de 6 dígitos que lhe enviamos por email sempre que iniciar sessão ou realizar ações sensíveis. Verifique o seu email para obter um código de 6 dígitos para completar a configuração.',

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
                'label' => 'Ativar códigos de verificação por email',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Códigos de verificação por email foram ativados',
        ],

    ],

];
