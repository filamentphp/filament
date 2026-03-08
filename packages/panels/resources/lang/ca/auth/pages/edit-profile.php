<?php

return [

    'label' => 'Perfil',

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'name' => [
            'label' => 'Nom',
        ],

        'password' => [
            'label' => 'Nova contrasenya',
            'validation_attribute' => 'contrasenya',
        ],

        'password_confirmation' => [
            'label' => 'Confirma la nova contrasenya',
            'validation_attribute' => 'confirmació de contrasenya',
        ],

        'current_password' => [
            'label' => 'Contrasenya actual',
            'below_content' => 'Per seguretat, si us plau, confirma la teva contrasenya per continuar.',
            'validation_attribute' => 'contrasenya actual',
        ],

        'actions' => [

            'save' => [
                'label' => 'Desar canvis',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Autenticació de doble factor (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Sol·licitud de canvi d\'adreça de correu electrònica enviada',
            'body' => 'S\'ha enviat una sol·licitud per canviar la seva adreça de correu electrònic a :email. Si us plau, revisa el correu per confirmar el canvi.',
        ],

        'saved' => [
            'title' => 'Canvis desats',
        ],

        'throttled' => [
            'title' => 'Massa intents. Si us plau, prova-ho de nou en :seconds segons.',
            'body' => 'Si us plau, prova-ho de nou en :seconds segons.',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Cancel·lar',
        ],

    ],

];
