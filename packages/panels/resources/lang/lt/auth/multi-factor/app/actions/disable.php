<?php

return [

    'label' => 'Išjungti',

    'modal' => [

        'heading' => 'Išjungti autentifikavimo programą',

        'description' => 'Ar tikrai norite sustabdyti autentifikavimo programos naudojimą? Išjungus šią funkciją, jūsų paskyrai bus pašalintas papildomas saugos lygis.',

        'form' => [

            'code' => [

                'label' => 'Įveskite 6 skaitmenų kodą iš autentifikavimo programos',

                'validation_attribute' => 'code',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Naudoti atsarginį kodą',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Įvestas kodas yra neteisingas.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Arba įveskite atsarginį kodą',

                'validation_attribute' => 'recovery code',

                'messages' => [

                    'invalid' => 'Įvestas atsarginis kodas yra neteisingas.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Išjungti autentifikavimo programą',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Autentifikavimo programa išjungta',
        ],

    ],

];
