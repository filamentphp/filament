<?php

return [

    'label' => 'Perfil',

    'form' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'name' => [
            'label' => 'Nome',
        ],

        'password' => [
            'label' => 'Nova senha',
            'validation_attribute' => 'senha',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar nova senha',
            'validation_attribute' => 'confirmação de senha',
        ],

        'current_password' => [
            'label' => 'Senha atual',
            'below_content' => 'Por segurança, confirme sua senha para continuar.',
            'validation_attribute' => 'senha atual',
        ],

        'actions' => [

            'save' => [
                'label' => 'Salvar alterações',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Autenticação de dois fatores (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Solicitação de alteração de e-mail enviada',
            'body' => 'Uma solicitação para alterar seu endereço de e-mail foi enviada para :email. Verifique seu e-mail para confirmar a alteração.',
        ],

        'saved' => [
            'title' => 'Salvo',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Cancelar',
        ],

    ],

];
