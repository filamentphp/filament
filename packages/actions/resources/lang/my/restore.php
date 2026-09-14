<?php

return [
    'single' => [
        'label' => 'ပြန်လည်ရယူရန်',
        'modal' => [
            'heading' => ':label ပြန်လည်ရယူရန်',
            'actions' => [
                'restore' => [
                    'label' => 'ပြန်လည်ရယူရန်',
                ],
            ],
        ],
        'notifications' => [
            'restored' => [
                'title' => 'ပြန်လည်ရယူပြီးပါပြီ',
            ],
        ],
    ],
    'multiple' => [
        'label' => 'ရွေးချယ်ထားသည်များကို ပြန်လည်ရယူရန်',
        'modal' => [
            'heading' => 'ရွေးချယ်ထားသော :label ကို ပြန်လည်ရယူရန်',
            'actions' => [
                'restore' => [
                    'label' => 'ပြန်လည်ရယူရန်',
                ],
            ],
        ],
        'notifications' => [
            'restored' => [
                'title' => 'ပြန်လည်ရယူပြီးပါပြီ',
            ],
            'restored_partial' => [
                'title' => 'စုစုပေါင်း :total ခုအနက် :count ခုကို ပြန်လည်ရယူပြီးပါပြီ',
                'missing_authorization_failure_message' => ':count ခုကို ပြန်လည်ရယူရန် သင့်တွင် ခွင့်ပြုချက်မရှိပါ။',
                'missing_processing_failure_message' => ':count ခုကို ပြန်လည်ရယူ၍မရပါ။',
            ],
            'restored_none' => [
                'title' => 'ပြန်လည်ရယူ၍မရပါ',
                'missing_authorization_failure_message' => ':count ခုကို ပြန်လည်ရယူရန် သင့်တွင် ခွင့်ပြုချက်မရှိပါ။',
                'missing_processing_failure_message' => ':count ခုကို ပြန်လည်ရယူ၍မရပါ။',
            ],
        ],
    ],
];
