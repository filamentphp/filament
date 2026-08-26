<?php

return [

    'management_schema' => [
        'actions' => [
            'label' => 'Programu ya uthibitishaji',

            'below_content' => 'Tumia programu salama kutengeneza msimbo wa muda wa kuthibitisha kuingia.',

            'messages' => [
                'enabled' => 'Imewashwa',
                'disabled' => 'Imezimwa',
            ],
        ],
    ],

    'login_form' => [
        'label' => 'Tumia msimbo kutoka programu yako ya uthibitishaji',

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
            ],
        ],

        'recovery_code' => [
            'label' => 'Au, weka kificho cha urejesho',

            'validation_attribute' => 'kificho cha urejesho',

            'messages' => [
                'invalid' => 'Kificho cha urejesho ulichouweka ni batili.',
            ],
        ],
    ],

];
