<?php

return [

    'title' => 'Taasta oma parool',

    'heading' => 'Unustasid parooli?',

    'actions' => [

        'login' => [
            'label' => 'tagasi sisselogimise juurde',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-posti aadress',
        ],

        'actions' => [

            'request' => [
                'label' => 'Saada e-kiri',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Kui teie kontot ei eksisteeri, ei saa te e-kirja.',
        ],

        'throttled' => [
            'title' => 'Liiga palju taotlusi',
            'body' => 'Palun proovige uuesti :seconds sekundi pärast.',
        ],

    ],

];
