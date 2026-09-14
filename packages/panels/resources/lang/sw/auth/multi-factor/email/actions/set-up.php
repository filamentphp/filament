<?php

return [

    'label' => 'Weka',

    'modal' => [
        'heading' => 'Weka misimbo ya uthibitishaji wa barua pepe',

        'description' => 'Utahitaji kuweka msimbo wenye tarakimu 6 tunaoikutumia kwa barua pepe kila unapoingia au kufanya vitendo nyeti. Angalia barua pepe yako kupata msimbo wa tarakimu 6 ili kukamilisha usanidi.',

        'form' => [
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
                    'rate_limited' => 'Majaribio mengi mno. Tafadhali jaribu tena baadaye.',
                ],
            ],
        ],

        'actions' => [
            'submit' => [
                'label' => 'Washa misimbo ya uthibitishaji wa barua pepe',
            ],
        ],
    ],

    'notifications' => [
        'enabled' => [
            'title' => 'Misimbo ya uthibitishaji wa barua pepe imewashwa',
        ],
    ],

];
