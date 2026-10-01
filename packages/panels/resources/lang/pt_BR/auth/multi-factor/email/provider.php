<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Códigos de verificação por e-mail',

            'below_content' => 'Receba um código temporário no seu e-mail para verificar sua identidade durante o login.',

            'messages' => [
                'enabled' => 'Ativado',
                'disabled' => 'Desativado',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Enviar um código para seu e-mail',

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

                        'throttled' => [
                            'title' => 'Muitas tentativas de reenvio. Aguarde antes de solicitar outro código.',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'O código informado é inválido.',

            ],

        ],

    ],

];
