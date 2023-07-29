<?php

return [

    'title' => 'Logga in',

    'heading' => 'Logga in på ditt konto',

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

        'actions' => [

            'authenticate' => [
                'label' => 'Logga in',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Inloggningen matchar inte våra uppgifter.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'För många inloggningsförsök. Vänligen försök igen om :seconds sekunder.',
        ],

    ],

];
