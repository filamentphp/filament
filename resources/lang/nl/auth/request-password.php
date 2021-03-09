<?php

return [

    'buttons' => [

        'submit' => [
            'label' => 'Verstuur wachtwoord herstel link',
        ],

    ],

    'form' => [

        'email' => [
            'hint' => 'Terug naar login',
            'label' => 'Email adres',
        ],

    ],

    'messages' => [

        'throttled' => 'Teveel inlogpogingen. Probeer het opnieuw over :seconds seconden.',

        'passwords' => [
            'sent' => 'We hebben je wachtwoord reset link gemailed!',
            'throttled' => 'Wacht voordat je het opnieuw probeert.',
            'user' => 'We kunnen geen gebruiker vinden met dat email adres.',
        ],

    ],

    'title' => 'Herstel wachtwoord',

];
