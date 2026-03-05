<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Autentifikavimo programa',

            'below_content' => 'Naudokite saugią programą, kad sugeneruotumėte laikiną kodą prisijungimui.',

            'messages' => [
                'enabled' => 'Įjungta',
                'disabled' => 'Išjungta',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Naudokite kodą iš savo autentifikavimo programos',

        'code' => [

            'label' => 'Įveskite 6 skaitmenų kodą iš autentifikavimo programos',

            'validation_attribute' => 'code',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Arba naudokite atsarginį kodą',
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

];
