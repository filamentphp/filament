<?php

return [

    'label' => 'Išjungti',

    'modal' => [

        'heading' => 'Išjungti el. pašto patvirtinimo kodus',

        'description' => 'Ar tikrai norite nustoti gauti el. pašto patvirtinimo kodus? Tai panaikins papildomą apsaugos sluoksnį jūsų paskyroje.',

        'form' => [

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

                    'rate_limited' => 'Per daug bandymų. Pabandykite vėliau.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Išjungti el. pašto patvirtinimo kodus',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'El. pašto patvirtinimo kodai išjungti',
        ],

    ],

];
