<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'Adres e-mail',
        ],

        'name' => [
            'label' => 'Nazwa',
        ],

        'password' => [
            'label' => 'Nowe hasło',
            'validation_attribute' => 'hasło',
        ],

        'password_confirmation' => [
            'label' => 'Potwierdź nowe hasło',
            'validation_attribute' => 'potwierdzenie hasła',
        ],

        'current_password' => [
            'label' => 'Aktualne hasło',
            'below_content' => 'Podaj swoje aktualne hasło, aby kontynuować.',
            'validation_attribute' => 'aktualne hasło',
        ],

        'actions' => [

            'save' => [
                'label' => 'Zapisz zmiany',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Uwierzytelnianie dwuskładnikowe (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Wysłano prośbę o zmianę adresu e-mail',
            'body' => 'Prośba o zmianę adresu e-mail została wysłana na adres :email. Sprawdź swoją skrzynkę, aby zweryfikować zmianę.',
        ],

        'saved' => [
            'title' => 'Zapisano',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Anuluj',
        ],

    ],

];
