<?php

return [

    'title' => 'የይለፍቃልዎን ያድሱ',
    'heading' => 'የይለፍቃልዎን ያድሱ',
    'form' => [

        'email' => [

            'label' => 'የኢሜይል አድራሻ',
        ],
        'password' => [

            'label' => 'የይለፍቃል',
            'validation_attribute' => 'password',
        ],
        'password_confirmation' => [

            'label' => 'የይለፍቃሉን ያረጋግጡ',
        ],
        'actions' => [

            'reset' => [

                'label' => 'የይለፍቃሉን ይቀይሩ',
            ],
        ],
    ],
    'notifications' => [

        'throttled' => [

            'title' => ' በጣም ብዙ ሙከራዎች',
            'body' => 'እባክዎ በ:seconds ሰከንድ ውስጥ እንደገና ይሞክሩ።',
        ],
    ],
];
