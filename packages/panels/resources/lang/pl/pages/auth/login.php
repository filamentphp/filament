<?php

return [

    'title' => 'Logowanie',

    'heading' => 'Zaloguj się',

    'actions' => [

        'authenticate' => [
            'label' => 'Zaloguj się',
        ],

    ],

    'form' => [

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

];
