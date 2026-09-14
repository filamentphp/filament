<?php

return [
    'title' => 'သင့်စကားဝှက်ကို ပြန်လည်သတ်မှတ်ပါ',
    'heading' => 'သင့်စကားဝှက်ကို ပြန်လည်သတ်မှတ်ပါ',
    'form' => [
        'email' => [
            'label' => 'အီးမေးလ်လိပ်စာ',
        ],
        'password' => [
            'label' => 'စကားဝှက်',
            'validation_attribute' => 'စကားဝှက်',
        ],
        'password_confirmation' => [
            'label' => 'စကားဝှက်ကို အတည်ပြုပါ',
        ],
        'actions' => [
            'reset' => [
                'label' => 'စကားဝှက် ပြန်လည်သတ်မှတ်ရန်',
            ],
        ],
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'ပြန်လည်သတ်မှတ်ရန် ကြိုးစားမှု များလွန်းနေပါသည်',
            'body' => ':seconds စက္ကန့်နောက် ထပ်စမ်းကြည့်ပါ။',
        ],
    ],
];
