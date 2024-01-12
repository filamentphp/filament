<?php

return [

    'title' => 'Hyr',

    'heading' => 'Hyr',

    'actions' => [

        'register' => [
            'before' => 'ose',
            'label' => 'rregjistro një llogari të re',
        ],

        'request_password_reset' => [
            'label' => 'Keni harruar fjalëkalimin?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'password' => [
            'label' => 'Fjalëkalimin',
        ],

        'remember' => [
            'label' => 'Mbaj mend fjalëkalimin',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Hyr',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Këto kredenciale nuk përputhen me të dhënat tona.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Shumë përpjekje për hyrje',
            'body' => 'Ju lutemi provoni përsëri në :seconds sekonda.',
        ],

    ],

];
