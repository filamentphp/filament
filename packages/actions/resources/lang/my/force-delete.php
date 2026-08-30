<?php

return [
    'single' => [
        'label' => 'အတင်းဖျက်ရန်',
        'modal' => [
            'heading' => ':label အတင်းဖျက်ရန်',
            'actions' => [
                'delete' => [
                    'label' => 'ဖျက်ရန်',
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
        'label' => 'ရွေးချယ်ထားသည်များကို အတင်းဖျက်ရန်',
        'modal' => [
            'heading' => 'ရွေးချယ်ထားသော :label များကို အတင်းဖျက်ရန်',
            'actions' => [
                'delete' => [
                    'label' => 'ဖျက်ရန်',
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
