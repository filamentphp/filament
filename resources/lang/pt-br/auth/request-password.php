<?php

return [

    'buttons' => [

        'submit' => [
            'label' => 'Enviar link de redefinição de senha',
        ],

    ],

    'form' => [

        'email' => [
            'hint' => 'Voltar ao login',
            'label' => 'E-mail',
        ],

    ],

    'messages' => [

        'throttled' => 'Muitas tentativas de login. Tente novamente em :seconds segundos.',

        'passwords' => [
            'sent' => 'Enviamos seu link de redefinição de senha por e-mail!',
            'throttled' => 'Aguarde antes de tentar novamente.',
            'user' => 'Não conseguimos encontrar um usuário com esse endereço de e-mail.',
        ],

    ],

    'title' => 'Redefinir senha',

];
