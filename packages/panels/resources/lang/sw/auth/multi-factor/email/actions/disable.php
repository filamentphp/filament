<?php

return [

    'label' => 'Zima',

    'modal' => [
        'heading' => 'Zima misimbo ya uthibitishaji wa barua pepe',

        'description' => 'Una uhakika unataka kuacha kupokea misimbo ya uthibitishaji wa barua pepe? Kuzima hii kutatondoa tabaka zaidi la usalama kwenye akaunti yako.',

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
                'label' => 'Zima misimbo ya uthibitishaji wa barua pepe',
            ],
        ],
    ],

    'notifications' => [
        'disabled' => [
            'title' => 'Misimbo ya uthibitishaji wa barua pepe imezimwa',
        ],
    ],

];
