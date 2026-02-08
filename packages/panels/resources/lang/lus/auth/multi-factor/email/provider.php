<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Email verification codes',

            'below_content' => 'Login lai in nangmah ngei ini tih chian nan I email address ah temporary code I dawng ang',

            'messages' => [
                'enabled' => 'Enabled',
                'disabled' => 'Disabled',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Email ah code thawn rawh',

        'code' => [

            'label' => '6-digit code email hmanga kan rawn thawn kha enter rawh',

            'validation_attribute' => 'code',

            'actions' => [

                'resend' => [

                    'label' => 'Email hmangin code thar thawnna',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Email ah code thar kan rawn thawn e',
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

];
