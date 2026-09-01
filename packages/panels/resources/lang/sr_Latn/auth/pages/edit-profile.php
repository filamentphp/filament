<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'Adresa e-pošte',
        ],

        'name' => [
            'label' => 'Ime',
        ],

        'password' => [
            'label' => 'Nova lozinka',
            'validation_attribute' => 'lozinka',
        ],

        'password_confirmation' => [
            'label' => 'Potvrdite novu lozinku',
            'validation_attribute' => 'potvrda lozinke',
        ],

        'current_password' => [
            'label' => 'Trenutna lozinka',
            'below_content' => 'Zbog bezbednosti morate da potvrdite lozinku za nastavak.',
            'validation_attribute' => 'trenutna lozinka',
        ],

        'actions' => [

            'save' => [
                'label' => 'Sačuvaj izmene',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Dvostruka autentifikacija (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Zahtev za izmenu adrese e-pošte je poslat',
            'body' => 'Zahtev za izmenu adrese e-pošte je poslat na :email. Proverite svoju e-poštu kako bi verifikovali promenu.',
        ],

        'saved' => [
            'title' => 'Sačuvano',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Odustani',
        ],

    ],

];
