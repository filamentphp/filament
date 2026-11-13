<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'El. pašto patvirtinimo kodai',

            'below_content' => 'Prisijungdami gaukite laikiną kodą savo el. pašto adresu, kad patvirtintumėte savo tapatybę.',

            'messages' => [
                'enabled' => 'Įgalinta',
                'disabled' => 'Išjungta',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Siųsti kodą į el. paštą',

        'code' => [

            'label' => 'Įveskite 6 skaitmenų kodą, kurį atsiuntėme el. paštu',

            'validation_attribute' => 'kodas',

            'actions' => [

                'resend' => [

                    'label' => 'Siųsti naują kodą el. paštu',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Išsiuntėme jums naują kodą el. paštu',
                        ],

                        'throttled' => [
                            'title' => 'Per daug bandymų siųsti kodą. Palaukite prieš prašydami naujo kodo.',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Įvestas kodas yra neteisingas.',

            ],

        ],

    ],

];
