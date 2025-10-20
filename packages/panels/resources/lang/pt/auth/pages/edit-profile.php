<?php

return [

    'label' => 'Perfil',

    'form' => [

        'email' => [
            'label' => 'Endereço de e-mail',
        ],

        'name' => [
            'label' => 'Nome',
        ],

        'password' => [
            'label' => 'Nova palavra-passe',
            'validation_attribute' => 'palavra-passe',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar nova palavra-passe',
            'validation_attribute' => 'confirmação da palavra-passe',
        ],

        'current_password' => [
            'label' => 'Palavra-passe actual',
            'below_content' => 'Por segurança, confirme a sua palavra-passe para continuar.',
            'validation_attribute' => 'palavra-passe actual',
        ],

        'actions' => [

            'save' => [
                'label' => 'Guardar alterações',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Autenticação de dois factores (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Pedido de alteração de endereço de e-mail enviado',
            'body' => 'Foi enviado um pedido para alterar o seu endereço de e-mail para :email. Verifique o seu e-mail para confirmar a alteração.',
        ],

        'saved' => [
            'title' => 'Guardado',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Cancelar',
        ],

    ],

];
