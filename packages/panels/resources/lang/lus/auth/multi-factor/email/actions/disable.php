<?php

return [

    'label' => 'Turn off',

    'modal' => [

        'heading' => 'Email verification codes tihtawp na',

        'description' => 'Email verification codes dawn hi tihtawp i duh tak tak em? Hemi tihtawp hian security dang I account a a pek belh ho a paih dawn ani.',

        'form' => [

            'code' => [

                'label' => '6-digit code email hmanga kan rawn thawn kha enter rawh',

                'validation_attribute' => 'code',

                'actions' => [

                    'resend' => [

                        'label' => 'Email ah code thar thawn rawh',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Email hmangin code thar kan rawn thawn e',
                            ],

                            'throttled' => [
                                'title' => 'Thawnnawn tumna a tam lutuk, Khawngaihin code dang dîl leh hmain nghak phawt rawh.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Hemi code hi a diklo.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Email verification codes tihtawp na',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Email verification codes chu disabled ani',
        ],

    ],

];
