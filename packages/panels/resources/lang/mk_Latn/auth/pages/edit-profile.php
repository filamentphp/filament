<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'E-pošta adresa',
        ],

        'name' => [
            'label' => 'Ime',
        ],

        'password' => [
            'label' => 'Nova lozinka',
            'validation_attribute' => 'lozinka',
        ],

        'password_confirmation' => [
            'label' => 'Potvrdi nova lozinka',
            'validation_attribute' => 'potvrda na lozinka',
        ],

        'current_password' => [
            'label' => 'Tekovna lozinka',
            'below_content' => 'Za bezbednost, ve molime potvrdete ja vašata lozinka za da prodolžite.',
            'validation_attribute' => 'tekovna lozinka',
        ],

        'actions' => [

            'save' => [
                'label' => 'Začuvaj promeni',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Dvofaktornа avtentifikacija (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Baranje za promena na e-pošta adresata e isprateno',
            'body' => 'Baranje za promena na vašata e-pošta adresa e isprateno na :email. Ve molime proverete ja vašata e-pošta za da ja potvrdite promenata.',
        ],

        'saved' => [
            'title' => 'Začuvano',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Otkaži',
        ],

    ],

];
