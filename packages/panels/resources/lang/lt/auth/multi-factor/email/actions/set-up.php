<?php

return [

    'label' => 'Nustatyti',

    'modal' => [

        'heading' => 'Nustatyti el. pašto patvirtinimo kodus',

        'description' => 'Kiekvieną kartą prisijungdami arba atlikdami jautrius veiksmus turėsite įvesti 6 skaitmenų kodą, kurį jums atsiųsime el. paštu. Patikrinkite savo el. paštą, kad gautumėte 6 skaitmenų kodą ir užbaigtumėte nustatymą.',

        'form' => [

            'code' => [

                'label' => 'Įveskite 6 skaitmenų kodą, kurį jums atsiuntėme el. paštu',

                'validation_attribute' => 'code',

                'actions' => [

                    'resend' => [

                        'label' => 'Siųsti naują kodą el. paštu',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Naujas kodas buvo išsiųstas el. paštu',
                            ],

                            'throttled' => [
                                'title' => 'Per daug bandymų siųsti kodą. Palaukite prieš bandydami dar kartą.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Įvestas kodas yra neteisingas.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Įgalinti el. pašto patvirtinimo kodus',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'El. pašto patvirtinimo kodai įgalinti',
        ],

    ],

];
