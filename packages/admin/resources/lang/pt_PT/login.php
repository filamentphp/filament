<?php

return [

    'title' => 'Login',

    'heading' => 'Iniciar sessão',

    'buttons' => [

        'submit' => [
            'label' => 'Login',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'password' => [
            'label' => 'Senha',
        ],

        'remember' => [
            'label' => 'Manter sessão',
        ],

    ],

    'messages' => [
        'failed' => 'As credênciais não correspondem aos nossos registos.',
        'throttled' => 'Muitas tentativas de login. Por favor, aguarde :seconds segundos para tentar novamente.',
    ],

];
