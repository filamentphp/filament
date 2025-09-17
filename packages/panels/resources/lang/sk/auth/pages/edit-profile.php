<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'Emailová adresa',
        ],

        'name' => [
            'label' => 'Meno',
        ],

        'password' => [
            'label' => 'Nové heslo',
            'validation_attribute' => 'heslo',
        ],

        'password_confirmation' => [
            'label' => 'Potvrdiť nové heslo',
            'validation_attribute' => 'potvrdenie hesla',
        ],

        'current_password' => [
            'label' => 'Aktuálne heslo',
            'below_content' => 'Z dôvodu bezpečnosti potvrďte svoje heslo pre pokračovanie.',
            'validation_attribute' => 'aktuálne heslo',
        ],

        'actions' => [

            'save' => [
                'label' => 'Uložiť zmeny',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Dvojfaktorové overenie (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Požiadavka na zmenu e-mailovej adresy bola odoslaná',
            'body' => 'Požiadavka na zmenu Vašej e-mailovej adresy bola odoslaná na :email. Skontrolujte si e-mail a overte zmenu.',
        ],

        'saved' => [
            'title' => 'Uložené',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Zrušiť',
        ],

    ],

];
