<?php

return [

    'management_schema' => [
        'actions' => [
            'label' => 'Misimbo ya uthibitishaji wa barua pepe',

            'below_content' => 'Pokea msimbo wa muda kwenye barua pepe yako ili kuthibitisha utambulisho wako wakati wa kuingia.',

            'messages' => [
                'enabled' => 'Imewashwa',
                'disabled' => 'Imezimwa',
            ],
        ],
    ],

    'login_form' => [
        'label' => 'Tuma msimbo kwenye barua pepe yako',

        'code' => [
            'label' => 'Weka msimbo wa tarakimu 6 tulioikutumia kwa barua pepe',

            'validation_attribute' => 'msimbo',

            'actions' => [
                'resend' => [
                    'label' => 'Tuma msimbo mpya kwa barua pepe',

                    'notifications' => [
                        'resent' => [
                            'title' => 'Tumekutumia msimbo mpya kwa barua pepe',
                        ],

                        'throttled' => [
                            'title' => 'Majaribio mengi mno ya kutuma tena. Tafadhali subiri kabla ya kuomba msimbo mwingine.',
                        ],
                    ],
                ],
            ],

            'messages' => [
                'invalid' => 'Msimbo uliouweka ni batili.',
            ],
        ],
    ],

];
