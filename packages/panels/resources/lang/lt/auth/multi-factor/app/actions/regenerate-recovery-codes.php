<?php

return [

    'label' => 'Regeneruoti atsarginius kodus',

    'modal' => [

        'heading' => 'Regeneruoti autentifikavimo programos atsarginius kodus',

        'description' => 'Jei praradote atsarginius kodus, galite juos regeneruoti čia. Jūsų seni atsarginiai kodai bus nedelsiant panaikinti.',

        'form' => [

            'code' => [

                'label' => 'Įveskite 6 skaitmenų kodą iš autentifikavimo programos',

                'validation_attribute' => 'code',

                'messages' => [

                    'invalid' => 'Įvestas kodas yra neteisingas.',

                    'rate_limited' => 'Per daug bandymų. Pabandykite vėliau.',

                ],

            ],

            'password' => [

                'label' => 'Arba įveskite dabartinį slaptažodį',

                'validation_attribute' => 'password',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Regeneruoti atsarginius kodus',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Nauji autentifikavimo programos atsarginiai kodai buvo sugeneruoti',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Nauji atsarginiai kodai',

            'description' => 'Prašome išsaugoti šiuos naujus atsarginius kodus saugioje vietoje. Jie bus rodomi tik vieną kartą, bet jums reikės jų, jei prarasite prieigą prie autentifikavimo programos:',

            'actions' => [

                'submit' => [
                    'label' => 'Uždaryti',
                ],

            ],

        ],

    ],

];
