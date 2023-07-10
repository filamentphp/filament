<?php

return [

    'title' => 'Logga in',

    'heading' => 'Logga in på ditt konto',

    'actions' => [

        'authenticate' => [
            'label' => 'Logga in',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-postadress',
        ],

        'password' => [
            'label' => 'Lösenord',
        ],

        'remember' => [
            'label' => 'Kom ihåg mig',
        ],

    ],

    'messages' => [
        'failed' => 'Inloggningen matchar inte våra uppgifter.',
        'throttled' => 'För många inloggningsförsök. Vänligen försök igen om :seconds sekunder.',
    ],

];
