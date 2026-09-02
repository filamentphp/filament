<?php

return [

    'label' => 'Zima',

    'modal' => [
        'heading' => 'Zima programu ya uthibitishaji',

        'description' => 'Una uhakika unataka kuacha kutumia programu ya uthibitishaji? Kuzima hii kutatondoa tabaka zaidi la usalama kwenye akaunti yako.',

        'form' => [
            'code' => [
                'label' => 'Weka msimbo wa tarakimu 6 kutoka programu ya uthibitishaji',

                'validation_attribute' => 'msimbo',

                'actions' => [
                    'use_recovery_code' => [
                        'label' => 'Tumia kificho cha urejesho badala yake',
                    ],
                ],

                'messages' => [
                    'invalid' => 'Msimbo uliouweka ni batili.',
                    'rate_limited' => 'Majaribio mengi mno. Tafadhali jaribu tena baadaye.',
                ],
            ],

            'recovery_code' => [
                'label' => 'Au, weka kificho cha urejesho',

                'validation_attribute' => 'kificho cha urejesho',

                'messages' => [
                    'invalid' => 'Kificho cha urejesho ulichouweka ni batili.',
                    'rate_limited' => 'Majaribio mengi mno. Tafadhali jaribu tena baadaye.',
                ],
            ],
        ],

        'actions' => [
            'submit' => [
                'label' => 'Zima programu ya uthibitishaji',
            ],
        ],
    ],

    'notifications' => [
        'disabled' => [
            'title' => 'Programu ya uthibitishaji imezimwa',
        ],
    ],

];
