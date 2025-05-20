<?php

return [

    'label' => 'ထုတ်ယူရန်',

    'modal' => [

        'heading' => ':label ထုတ်ယူရန်',

        'form' => [

            'columns' => [

                'label' => 'ကော်လံများ',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column ကို ဖွင့်ထားသည်',
                    ],

                    'label' => [
                        'label' => ':column အမည်',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'ထုတ်ယူရန်',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'ထုတ်ယူခြင်း ပြီးဆုံးပါပြီ',

            'actions' => [

                'download_csv' => [
                    'label' => 'csv ဖိုင်ဖြင့် ဒေါင်းလုပ်ရန်',
                ],

                'download_xlsx' => [
                    'label' => 'xlsx ဖိုင်ဖြင့် ဒေါင်းလုပ်ရန်',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'ထုတ်ယူရန် ဒေတာပမာဏ များလွန်းနေပါသည်',
            'body' => 'တစ်ကြိမ်တည်းတွင် စာကြောင်းရေ :count ထက်ပိုပြီး ထုတ်ယူ၍မရပါ။',
        ],

        'started' => [
            'title' => 'ထုတ်ယူခြင်း စတင်ပါပြီ',
            'body' => 'ထုတ်ယူခြင်း စတင်ပါပြီ။ စာကြောင်းရေ :count ကို နောက်ကွယ်တွင် လုပ်ဆောင်နေပါသည်။',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
