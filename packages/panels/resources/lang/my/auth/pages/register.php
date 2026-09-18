<?php

return [
    'title' => 'စာရင်းသွင်းရန်',
    'heading' => 'အကောင့်ဖွင့်ရန်',
    'actions' => [
        'login' => [
            'before' => 'သို့မဟုတ်',
            'label' => 'သင့်အကောင့်သို့ ဝင်ရန်',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'အီးမေးလ်လိပ်စာ',
        ],
        'name' => [
            'label' => 'အမည်',
        ],
        'password' => [
            'label' => 'စကားဝှက်',
            'validation_attribute' => 'စကားဝှက်',
        ],
        'password_confirmation' => [
            'label' => 'စကားဝှက်ကို အတည်ပြုပါ',
        ],
        'actions' => [
            'register' => [
                'label' => 'အကောင့်ဖွင့်ရန်',
            ],
        ],
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'စာရင်းသွင်းရန် ကြိုးစားမှု များလွန်းနေပါသည်',
            'body' => ':seconds စက္ကန့်နောက် ထပ်စမ်းကြည့်ပါ။',
        ],
    ],
];
