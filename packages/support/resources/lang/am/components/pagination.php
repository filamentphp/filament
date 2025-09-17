<?php

return [

    'label' => 'የገጽታ አሰሳ',
    'overview' => '{1} ከ1 መዝገብ 1 በማሳየት ላይ |[2,*] ከጠቅላላ :total መዝገቦች ከ:first እስከ :last በማሳየት ላይ::',
    'fields' => [

        'records_per_page' => [

            'label' => 'በየ ገጽ',
            'options' => [

                'all' => 'ሁሉም',
            ],
        ],
    ],
    'actions' => [

        'first' => [

            'label' => 'መጀመርያ',
        ],
        'go_to_page' => [

            'label' => 'ወደ ገጽ :page ሂድ',
        ],
        'last' => [

            'label' => 'መጨረሻ',
        ],
        'next' => [

            'label' => 'ቀጣይ',
        ],
        'previous' => [

            'label' => 'ያለፈው',
        ],
    ],
];
