<?php

return [

    'label' => 'Profiili',

    'form' => [

        'email' => [
            'label' => 'Sähköpostiosoite',
        ],

        'name' => [
            'label' => 'Nimi',
        ],

        'password' => [
            'label' => 'Uusi salasana',
            'validation_attribute' => 'salasana',
        ],

        'password_confirmation' => [
            'label' => 'Vahvista salasana',
            'validation_attribute' => 'salasanan vahvistus',
        ],

        'current_password' => [
            'label' => 'Nykyinen salasana',
            'below_content' => 'Turvallisuuden vuoksi, vahvista salasanasi jatkaaksesi.',
            'validation_attribute' => 'nykyinen salasana',
        ],

        'actions' => [

            'save' => [
                'label' => 'Tallenna muutokset',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Kaksivaiheinen tunnistautuminen (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Sähköpostin vaihdon pyyntö on lähetetty',
            'body' => 'Pyyntö sähköpostin vaihdosta on lähetetty osoitteeseen :email. Tarkista sähköpostisi vahvistaaksesi muutoksen.',
        ],

        'saved' => [
            'title' => 'Tallennettu',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Takaisin',
        ],

    ],

];
