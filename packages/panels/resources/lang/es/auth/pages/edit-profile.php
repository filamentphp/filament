<?php

return [

    'label' => 'Perfil',

    'form' => [

        'email' => [
            'label' => 'Dirección Email',
        ],

        'name' => [
            'label' => 'Nombre',
        ],

        'password' => [
            'label' => 'Nueva contraseña',
            'validation_attribute' => 'contraseña',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar nueva contraseña',
            'validation_attribute' => 'confirmación de contraseña',
        ],

        'current_password' => [
            'label' => 'Contraseña actual',
            'below_content' => 'Por seguridad, por favor confirme su contraseña para continuar.',
            'validation_attribute' => 'contraseña actual',
        ],

        'actions' => [

            'save' => [
                'label' => 'Guardar cambios',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Autenticación de dos factores (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Solicitud de cambio de correo electrónico enviada',
            'body' => 'Se ha enviado una solicitud para cambiar su dirección de correo electrónico a :email. Por favor, revise su correo para confirmar el cambio.',
        ],

        'saved' => [
            'title' => 'Cambios guardados',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Regresar',
        ],

    ],

];
