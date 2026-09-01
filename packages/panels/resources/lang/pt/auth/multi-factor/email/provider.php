<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Códigos de verificação por email',

            'below_content' => 'Receba um código temporário no seu endereço de email para verificar a sua identidade durante o início de sessão.',

            'messages' => [
                'enabled' => 'Ativados',
                'disabled' => 'Desativados',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Enviar um código para o seu email',

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

];
