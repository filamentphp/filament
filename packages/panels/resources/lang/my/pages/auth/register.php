<?php

return [

    'title' => 'အကောင့်အသစ်ဖွင့်ရန်',

    'heading' => 'အကောင့်အသစ်ဖွင့်ပါ',

    'actions' => [

        'login' => [
            'before' => 'သို့မဟုတ်',
            'label' => 'အကောင့်ဝင်ရန်',
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
            'label' => 'စကားဝှက်အတည်ပြုပါ',
        ],

        'actions' => [

            'register' => [
                'label' => 'အကောင့်ဖွင့်မည်',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'အကောင့်ဖွင့်ရန် ကြိုးပမ်းမှု များလွန်းနေပါသည်',
            'body' => 'ကျေးဇူးပြု၍ စက္ကန့် :seconds မှ ပြန်လည်ကြိုးစားပါ။',
        ],

    ],

];
