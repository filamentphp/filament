<?php

return [

    'title' => 'ምዝገባ',
    'heading' => 'ምዝገባን አከናውን',
    'actions' => [

        'login' => [

            'before' => 'ወይም',
            'label' => 'ወደ በመለያዎ ይግቡ',
        ],
    ],
    'form' => [

        'email' => [

            'label' => 'የ ኢሜል አድራሻ',
        ],
        'name' => [

            'label' => 'ስም',
        ],
        'password' => [

            'label' => 'የይለፍ ቃል',
            'validation_attribute' => 'የይለፍ ቃል',
        ],
        'password_confirmation' => [

            'label' => 'የይለፍ ቃልዎን ያጽድቁ',
        ],
        'actions' => [

            'register' => [

                'label' => 'ተመዝገብ',
            ],
        ],
    ],
    'notifications' => [

        'throttled' => [

            'title' => 'በጣም ብዙ የምዝገባ ሙከራዎች',
            'body' => 'እባክዎ በ:seconds ሰከንዶች ውስጥ እንደገና ይሞክሩ።',
        ],
    ],
];
