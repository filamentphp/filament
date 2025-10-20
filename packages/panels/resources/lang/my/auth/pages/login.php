<?php

return [

    'title' => 'အကောင့်ဝင်ရန်',

    'heading' => 'အကောင့်ဝင်ပါ',

    'actions' => [

        'register' => [
            'before' => 'သို့မဟုတ်',
            'label' => 'အကောင့်အသစ်ဖွင့်ရန်',
        ],

        'request_password_reset' => [
            'label' => 'စကားဝှက်မေ့နေပါသလား?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'အီးမေးလ်လိပ်စာ',
        ],

        'password' => [
            'label' => 'စကားဝှက်',
        ],

        'remember' => [
            'label' => 'မှတ်ထားပါ',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'ဝင်ရောက်မည်',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'ဖြည့်သွင်းထားသော အချက်အလက်များ မှားယွင်းနေပါသည်။',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'အကောင့်ဝင်ရန် ကြိုးပမ်းမှု များလွန်းနေပါသည်',
            'body' => 'ကျေးဇူးပြု၍ စက္ကန့် :seconds မှ ပြန်လည်ကြိုးစားပါ။',
        ],

    ],

];
