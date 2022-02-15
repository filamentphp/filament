<?php

return [

    'title' => 'Logowanie',

    'buttons' => [

        'submit' => [
            'label' => 'Zaloguj się',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Adres e-mail',
        ],

        'password' => [
            'label' => 'Hasło',
        ],

        'remember' => [
            'label' => 'Zapamiętaj mnie',
        ],

    ],

    'messages' => [
        'failed' => 'Błędny login lub hasło.',
        'throttled' => 'Za dużo nieudanych prób logowania. Proszę spróbować za :seconds sekund.',
    ],

    'heading' => 'Zaloguj się do swojego konta',

];
