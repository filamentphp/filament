<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'Adresă de email',
        ],

        'name' => [
            'label' => 'Nume',
        ],

        'password' => [
            'label' => 'Parolă nouă',
            'validation_attribute' => 'parolă',
        ],

        'password_confirmation' => [
            'label' => 'Confirmă parola nouă',
            'validation_attribute' => 'confirmare parolă',
        ],

        'current_password' => [
            'label' => 'Parola curentă',
            'below_content' => 'Pentru securitate, vă rugăm să confirmați parola pentru a continua.',
            'validation_attribute' => 'parola curentă',
        ],

        'actions' => [

            'save' => [
                'label' => 'Salvează modificările',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Autentificare cu doi factori (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Cerere de schimbare a adresei de email trimisă',
            'body' => 'O cerere de schimbare a adresei de email a fost trimisă la :email. Vă rugăm să verificați emailul pentru a confirma schimbarea.',
        ],

        'saved' => [
            'title' => 'Salvat cu succes',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Anulare',
        ],

    ],

];
