<?php

return [

    'label' => 'Tengeneza upya vificho vya urejesho',

    'modal' => [
        'heading' => 'Tengeneza upya vificho vya urejesho vya programu ya uthibitishaji',

        'description' => 'Ukipoteza vificho vyako vya urejesho, unaweza kuvitengeneza upya hapa. Vificho vyako vya zamani vita batilishwa mara moja.',

        'form' => [
            'code' => [
                'label' => 'Weka msimbo wa tarakimu 6 kutoka programu ya uthibitishaji',

                'validation_attribute' => 'msimbo',

                'messages' => [
                    'invalid' => 'Msimbo uliouweka ni batili.',
                    'rate_limited' => 'Majaribio mengi mno. Tafadhali jaribu tena baadaye.',
                ],
            ],

            'password' => [
                'label' => 'Au, weka nenosiri lako la sasa',
                'validation_attribute' => 'nenosiri',
            ],
        ],

        'actions' => [
            'submit' => [
                'label' => 'Tengeneza upya vificho vya urejesho',
            ],
        ],
    ],

    'notifications' => [
        'regenerated' => [
            'title' => 'Vificho vipya vya urejesho vya programu ya uthibitishaji vimetengenezwa',
        ],
    ],

    'show_new_recovery_codes' => [
        'modal' => [
            'heading' => 'Vificho vipya vya urejesho',

            'description' => 'Tafadhali hifadhi vificho vifavyo vya urejesho mahali salama. Vitaonyeshwa mara moja tu, lakini utahitaji ukipoteza ufikiaji wa programu yako ya uthibitishaji:',

            'actions' => [
                'submit' => [
                    'label' => 'Funga',
                ],
            ],
        ],
    ],

];
