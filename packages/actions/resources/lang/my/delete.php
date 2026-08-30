<?php

return [
    'single' => [
        'label' => 'ဖျက်ပါ',
        'modal' => [
            'heading' => ':label ကိုဖျက်ပါ',
            'actions' => [
                'delete' => [
                    'label' => 'ဖျက်ပါ',
                ],
            ],
        ],
        'notifications' => [
            'deleted' => [
                'title' => 'ဖျက်ပြီးပါပြီ',
            ],
        ],
    ],
    'multiple' => [
        'label' => 'ဖျက်ပါ',
        'modal' => [
            'heading' => 'ရွေးချယ်ထားသည့် :label (များ)အား ဖျက်ပါ',
            'actions' => [
                'delete' => [
                    'label' => 'ဖျက်ပါ',
                ],
            ],
        ],
        'notifications' => [
            'deleted' => [
                'title' => 'ဖျက်ပြီးပါပြီ',
            ],
            'deleted_partial' => [
                'title' => 'စုစုပေါင်း :total ခုအနက် :count ခုကို ဖျက်ပြီးပါပြီ',
                'missing_authorization_failure_message' => ':count ခုကို ဖျက်ရန် သင့်တွင် ခွင့်ပြုချက်မရှိပါ။',
                'missing_processing_failure_message' => ':count ခုကို ဖျက်၍မရပါ။',
            ],
            'deleted_none' => [
                'title' => 'ဖျက်၍မရပါ',
                'missing_authorization_failure_message' => ':count ခုကို ဖျက်ရန် သင့်တွင် ခွင့်ပြုချက်မရှိပါ။',
                'missing_processing_failure_message' => ':count ခုကို ဖျက်၍မရပါ။',
            ],
        ],
    ],
];
