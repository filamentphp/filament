<?php

return [
    'label' => 'မေးမြန်းချက် တည်ဆောက်ကိရိယာ',
    'form' => [
        'operator' => [
            'label' => 'နှိုင်းယှဉ်ပုံ',
        ],
        'or_groups' => [
            'label' => 'အုပ်စုများ',
            'group' => [
                'label' => 'အုပ်စု',
            ],
            'block' => [
                'label' => 'သို့မဟုတ် အခြေအနေ',
                'or' => 'သို့မဟုတ်',
            ],
        ],
        'rules' => [
            'label' => 'စည်းမျဉ်းများ',
            'item' => [
                'and' => 'နှင့်',
            ],
        ],
    ],
    'no_rules' => '(စည်းမျဉ်း မရှိပါ)',
    'max_rules_reached_tooltip' => 'စည်းမျဉ်း အများဆုံး :count ခုအထိ ရောက်ရှိပါပြီ။',
    'item_separators' => [
        'and' => 'နှင့်',
        'or' => 'သို့မဟုတ်',
    ],
    'operators' => [
        'is_filled' => [
            'label' => [
                'direct' => 'ဖြည့်ထားသည်',
                'inverse' => 'ဗလာဖြစ်သည်',
            ],
            'summary' => [
                'direct' => ':attribute ကို ဖြည့်ထားသည်',
                'inverse' => ':attribute သည် ဗလာဖြစ်သည်',
            ],
        ],
        'boolean' => [
            'is_true' => [
                'label' => [
                    'direct' => 'မှန်သည်',
                    'inverse' => 'မှားသည်',
                ],
                'summary' => [
                    'direct' => ':attribute သည် မှန်သည်',
                    'inverse' => ':attribute သည် မှားသည်',
                ],
            ],
        ],
        'date' => [
            'is_after' => [
                'label' => [
                    'direct' => 'နောက်ပိုင်း ဖြစ်သည်',
                    'inverse' => 'နောက်ပိုင်း မဟုတ်ပါ',
                ],
                'summary' => [
                    'direct' => ':attribute သည် :date နောက်ပိုင်းဖြစ်သည်',
                    'inverse' => ':attribute သည် :date နောက်ပိုင်း မဟုတ်ပါ',
                ],
            ],
            'is_before' => [
                'label' => [
                    'direct' => 'မတိုင်မီ ဖြစ်သည်',
                    'inverse' => 'မတိုင်မီ မဟုတ်ပါ',
                ],
                'summary' => [
                    'direct' => ':attribute သည် :date မတိုင်မီဖြစ်သည်',
                    'inverse' => ':attribute သည် :date မတိုင်မီ မဟုတ်ပါ',
                ],
            ],
            'is_date' => [
                'label' => [
                    'direct' => 'ရက်စွဲ ဖြစ်သည်',
                    'inverse' => 'ရက်စွဲ မဟုတ်ပါ',
                ],
                'summary' => [
                    'direct' => ':attribute သည် :date ဖြစ်သည်',
                    'inverse' => ':attribute သည် :date မဟုတ်ပါ',
                ],
            ],
            'is_month' => [
                'label' => [
                    'direct' => 'လ ဖြစ်သည်',
                    'inverse' => 'လ မဟုတ်ပါ',
                ],
                'summary' => [
                    'direct' => ':attribute သည် :month ဖြစ်သည်',
                    'inverse' => ':attribute သည် :month မဟုတ်ပါ',
                ],
            ],
            'is_year' => [
                'label' => [
                    'direct' => 'နှစ် ဖြစ်သည်',
                    'inverse' => 'နှစ် မဟုတ်ပါ',
                ],
                'summary' => [
                    'direct' => ':attribute သည် :year ဖြစ်သည်',
                    'inverse' => ':attribute သည် :year မဟုတ်ပါ',
                ],
            ],
            'unit_labels' => [
                'second' => 'စက္ကန့်များ',
                'minute' => 'မိနစ်များ',
                'hour' => 'နာရီများ',
                'day' => 'ရက်များ',
                'week' => 'သီတင်းပတ်များ',
                'month' => 'လများ',
                'quarter' => 'သုံးလပတ်များ',
                'year' => 'နှစ်များ',
            ],
            'presets' => [
                'past_decade' => 'လွန်ခဲ့သော ဆယ်စုနှစ်',
                'past_5_years' => 'လွန်ခဲ့သော ၅ နှစ်',
                'past_2_years' => 'လွန်ခဲ့သော ၂ နှစ်',
                'past_year' => 'လွန်ခဲ့သော နှစ်',
                'past_6_months' => 'လွန်ခဲ့သော ၆ လ',
                'past_quarter' => 'လွန်ခဲ့သော သုံးလပတ်',
                'past_month' => 'လွန်ခဲ့သော လ',
                'past_2_weeks' => 'လွန်ခဲ့သော ၂ ပတ်',
                'past_week' => 'လွန်ခဲ့သော သီတင်းပတ်',
                'past_hour' => 'လွန်ခဲ့သော နာရီ',
                'past_minute' => 'လွန်ခဲ့သော မိနစ်',
                'this_decade' => 'ယခု ဆယ်စုနှစ်',
                'this_year' => 'ယခုနှစ်',
                'this_quarter' => 'ယခု သုံးလပတ်',
                'this_month' => 'ယခုလ',
                'today' => 'ယနေ့',
                'this_hour' => 'ယခု နာရီ',
                'this_minute' => 'ယခု မိနစ်',
                'next_minute' => 'လာမည့် မိနစ်',
                'next_hour' => 'လာမည့် နာရီ',
                'next_week' => 'လာမည့် သီတင်းပတ်',
                'next_2_weeks' => 'လာမည့် ၂ ပတ်',
                'next_month' => 'လာမည့် လ',
                'next_quarter' => 'လာမည့် သုံးလပတ်',
                'next_6_months' => 'လာမည့် ၆ လ',
                'next_year' => 'လာမည့်နှစ်',
                'next_2_years' => 'လာမည့် ၂ နှစ်',
                'next_5_years' => 'လာမည့် ၅ နှစ်',
                'next_decade' => 'လာမည့် ဆယ်စုနှစ်',
                'custom' => 'စိတ်ကြိုက်',
            ],
            'form' => [
                'date' => [
                    'label' => 'ရက်စွဲ',
                ],
                'month' => [
                    'label' => 'လ',
                ],
                'year' => [
                    'label' => 'နှစ်',
                ],
                'mode' => [
                    'label' => 'ရက်စွဲ အမျိုးအစား',
                    'options' => [
                        'absolute' => 'သတ်မှတ်ရက်စွဲ',
                        'relative' => 'အချိန်အလိုက် ရွေ့လျားသည့် ကာလ',
                    ],
                ],
                'preset' => [
                    'label' => 'အချိန်ညွှန်းပုံ',
                ],
                'relative_value' => [
                    'label' => 'အရေအတွက်',
                ],
                'relative_unit' => [
                    'label' => 'အချိန်ယူနစ်',
                ],
                'tense' => [
                    'label' => 'အချိန်ကာလ',
                    'options' => [
                        'past' => 'အတိတ်',
                        'future' => 'အနာဂတ်',
                    ],
                ],
            ],
        ],
        'number' => [
            'equals' => [
                'label' => [
                    'direct' => 'ညီမျှသည်',
                    'inverse' => 'မညီမျှပါ',
                ],
                'summary' => [
                    'direct' => ':attribute သည် :number နှင့် ညီမျှသည်',
                    'inverse' => ':attribute သည် :number နှင့် မညီမျှပါ',
                ],
            ],
            'is_max' => [
                'label' => [
                    'direct' => 'အများဆုံး ဖြစ်သည်',
                    'inverse' => 'ပိုများသည်',
                ],
                'summary' => [
                    'direct' => ':attribute သည် အများဆုံး :number ဖြစ်သည်',
                    'inverse' => ':attribute သည် :number ထက် ပိုများသည်',
                ],
            ],
            'is_min' => [
                'label' => [
                    'direct' => 'အနည်းဆုံး ဖြစ်သည်',
                    'inverse' => 'ပိုနည်းသည်',
                ],
                'summary' => [
                    'direct' => ':attribute သည် အနည်းဆုံး :number ဖြစ်သည်',
                    'inverse' => ':attribute သည် :number ထက် ပိုနည်းသည်',
                ],
            ],
            'aggregates' => [
                'average' => [
                    'label' => 'ပျမ်းမျှ',
                    'summary' => ':attribute ၏ ပျမ်းမျှ',
                ],
                'max' => [
                    'label' => 'အများဆုံး',
                    'summary' => ':attribute ၏ အများဆုံးတန်ဖိုး',
                ],
                'min' => [
                    'label' => 'အနည်းဆုံး',
                    'summary' => ':attribute ၏ အနည်းဆုံးတန်ဖိုး',
                ],
                'sum' => [
                    'label' => 'စုစုပေါင်း',
                    'summary' => ':attribute ၏ စုစုပေါင်း',
                ],
            ],
            'form' => [
                'aggregate' => [
                    'label' => 'စုစည်းတွက်ချက်မှု',
                ],
                'number' => [
                    'label' => 'နံပါတ်',
                ],
            ],
        ],
        'relationship' => [
            'equals' => [
                'label' => [
                    'direct' => 'ရှိသည်',
                    'inverse' => 'မရှိပါ',
                ],
                'summary' => [
                    'direct' => ':relationship :count ခု ရှိသည်',
                    'inverse' => ':relationship :count ခု မရှိပါ',
                ],
            ],
            'has_max' => [
                'label' => [
                    'direct' => 'အများဆုံး ရှိသည်',
                    'inverse' => 'ပို၍ ရှိသည်',
                ],
                'summary' => [
                    'direct' => ':relationship အများဆုံး :count ခု ရှိသည်',
                    'inverse' => ':relationship :count ခုထက် ပို၍ ရှိသည်',
                ],
            ],
            'has_min' => [
                'label' => [
                    'direct' => 'အနည်းဆုံး ရှိသည်',
                    'inverse' => 'အရေအတွက် ပိုနည်းသည်',
                ],
                'summary' => [
                    'direct' => ':relationship အနည်းဆုံး :count ခု ရှိသည်',
                    'inverse' => ':relationship :count ခုထက် ပိုနည်း၍ ရှိသည်',
                ],
            ],
            'is_empty' => [
                'label' => [
                    'direct' => 'ဗလာဖြစ်သည်',
                    'inverse' => 'ဗလာမဟုတ်ပါ',
                ],
                'summary' => [
                    'direct' => ':relationship သည် ဗလာဖြစ်သည်',
                    'inverse' => ':relationship သည် ဗလာမဟုတ်ပါ',
                ],
            ],
            'is_related_to' => [
                'label' => [
                    'single' => [
                        'direct' => 'ဖြစ်သည်',
                        'inverse' => 'မဟုတ်ပါ',
                    ],
                    'multiple' => [
                        'direct' => 'ပါဝင်သည်',
                        'inverse' => 'မပါဝင်ပါ',
                    ],
                ],
                'summary' => [
                    'single' => [
                        'direct' => ':relationship သည် :values ဖြစ်သည်',
                        'inverse' => ':relationship သည် :values မဟုတ်ပါ',
                    ],
                    'multiple' => [
                        'direct' => ':relationship တွင် :values ပါဝင်သည်',
                        'inverse' => ':relationship တွင် :values မပါဝင်ပါ',
                    ],
                    'values_glue' => [
                        0 => '၊ ',
                        'final' => ' သို့မဟုတ် ',
                    ],
                ],
                'form' => [
                    'value' => [
                        'label' => 'တန်ဖိုး',
                    ],
                    'values' => [
                        'label' => 'တန်ဖိုးများ',
                    ],
                ],
            ],
            'form' => [
                'count' => [
                    'label' => 'အရေအတွက်',
                ],
            ],
        ],
        'select' => [
            'is' => [
                'label' => [
                    'direct' => 'ဖြစ်သည်',
                    'inverse' => 'မဟုတ်ပါ',
                ],
                'summary' => [
                    'direct' => ':attribute သည် :values ဖြစ်သည်',
                    'inverse' => ':attribute သည် :values မဟုတ်ပါ',
                    'values_glue' => [
                        0 => '၊ ',
                        'final' => ' သို့မဟုတ် ',
                    ],
                ],
                'form' => [
                    'value' => [
                        'label' => 'တန်ဖိုး',
                    ],
                    'values' => [
                        'label' => 'တန်ဖိုးများ',
                    ],
                ],
            ],
        ],
        'text' => [
            'contains' => [
                'label' => [
                    'direct' => 'ပါဝင်သည်',
                    'inverse' => 'မပါဝင်ပါ',
                ],
                'summary' => [
                    'direct' => ':attribute တွင် :text ပါဝင်သည်',
                    'inverse' => ':attribute တွင် :text မပါဝင်ပါ',
                ],
            ],
            'ends_with' => [
                'label' => [
                    'direct' => 'ဖြင့် အဆုံးသတ်သည်',
                    'inverse' => 'ဖြင့် အဆုံးမသတ်ပါ',
                ],
                'summary' => [
                    'direct' => ':attribute သည် :text ဖြင့် အဆုံးသတ်သည်',
                    'inverse' => ':attribute သည် :text ဖြင့် အဆုံးမသတ်ပါ',
                ],
            ],
            'equals' => [
                'label' => [
                    'direct' => 'ညီမျှသည်',
                    'inverse' => 'မညီမျှပါ',
                ],
                'summary' => [
                    'direct' => ':attribute သည် :text နှင့် ညီမျှသည်',
                    'inverse' => ':attribute သည် :text နှင့် မညီမျှပါ',
                ],
            ],
            'starts_with' => [
                'label' => [
                    'direct' => 'ဖြင့် စတင်သည်',
                    'inverse' => 'ဖြင့် မစတင်ပါ',
                ],
                'summary' => [
                    'direct' => ':attribute သည် :text ဖြင့် စတင်သည်',
                    'inverse' => ':attribute သည် :text ဖြင့် မစတင်ပါ',
                ],
            ],
            'form' => [
                'text' => [
                    'label' => 'စာသား',
                ],
            ],
        ],
    ],
    'actions' => [
        'add_rule' => [
            'label' => 'စည်းမျဉ်း ထည့်ရန်',
        ],
        'add_rule_group' => [
            'label' => 'သို့မဟုတ် ထည့်ရန်',
        ],
    ],
];
