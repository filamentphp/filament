<?php

return [

    'label' => 'Profilo',

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'name' => [
            'label' => 'Nome',
        ],

        'password' => [
            'label' => 'Nuova password',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Conferma nuova password',
            'validation_attribute' => 'password confirmation',
        ],

        'current_password' => [
            'label' => 'Password attuale',
            'below_content' => 'Per motivi di sicurezza, conferma la tua password per continuare.',
            'validation_attribute' => 'current password',
        ],

        'actions' => [

            'save' => [
                'label' => 'Salva modifiche',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Autenticazione a due fattori (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'La richiesta di modifica dell\'email è stata inviata',
            'body' => 'È stata inviata una richiesta per modificare il tuo indirizzo email a :email. Controlla la tua email per verificare la modifica.',
        ],

        'saved' => [
            'title' => 'Salvato',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Indietro',
        ],

    ],

];
