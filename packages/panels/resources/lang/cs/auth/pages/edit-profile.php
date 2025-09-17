<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'E-mailová adresa',
        ],

        'name' => [
            'label' => 'Jméno',
        ],

        'password' => [
            'label' => 'Nové heslo',
            'validation_attribute' => 'heslo',
        ],

        'password_confirmation' => [
            'label' => 'Potvrďte nové heslo',
            'validation_attribute' => 'potvrzení hesla',
        ],

        'current_password' => [
            'label' => 'Aktuální heslo',
            'below_content' => 'Z bezpečnostních důvodů potvrďte své heslo pro pokračování.',
            'validation_attribute' => 'aktuální heslo',
        ],

        'actions' => [

            'save' => [
                'label' => 'Uložit',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Dvoufaktorové ověření (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Požadavek na změnu e-mailové adresy byl odeslán',
            'body' => 'Požadavek na změnu Vaší e-mailové adresy byl odeslán na :email. Zkontrolujte si e-mail a potvrďte změnu.',
        ],

        'saved' => [
            'title' => 'Uloženo',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Zrušit',
        ],

    ],

];
