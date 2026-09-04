<?php

return [
    'column_manager' => [
        'heading' => 'ကော်လံများ',
        'actions' => [
            'apply' => [
                'label' => 'ကော်လံများကို အသုံးပြုရန်',
            ],
            'reorder' => [
                'label' => 'ကော်လံကို ပြန်စီရန်',
            ],
            'reset' => [
                'label' => 'မူလအတိုင်း ပြန်ထားရန်',
            ],
        ],
    ],
    'columns' => [
        'actions' => [
            'label' => 'လုပ်ဆောင်ချက်|လုပ်ဆောင်ချက်များ',
        ],
        'icon' => [
            'boolean' => [
                'true' => 'ဟုတ်သည်',
                'false' => 'မဟုတ်ပါ',
            ],
        ],
        'select' => [
            'loading_message' => 'ဖွင့်နေသည်...',
            'no_options_message' => 'ရွေးချယ်စရာ မရှိပါ။',
            'no_search_results_message' => 'သင်ရှာဖွေထားသည်နှင့် ကိုက်ညီသော ရွေးချယ်စရာ မရှိပါ။',
            'placeholder' => 'ရွေးချယ်စရာတစ်ခု ရွေးပါ',
            'searching_message' => 'ရှာဖွေနေသည်...',
            'search_prompt' => 'ရှာဖွေရန် စာရိုက်ပါ...',
        ],
        'text' => [
            'actions' => [
                'collapse_list' => ':count ခု လျှော့ပြရန်',
                'expand_list' => ':count ခု ထပ်ပြရန်',
            ],
            'more_list_items' => 'နှင့် နောက်ထပ် :count ခု',
        ],
    ],
    'fields' => [
        'bulk_select_page' => [
            'label' => 'အစုလိုက်လုပ်ဆောင်ရန် ပစ္စည်းအားလုံးကို ရွေး/မရွေးပါ။',
        ],
        'bulk_select_record' => [
            'label' => 'အစုလိုက်လုပ်ဆောင်ရန် ပစ္စည်း :key ကို ရွေး/မရွေးပါ။',
        ],
        'bulk_select_group' => [
            'label' => 'အစုလိုက်လုပ်ဆောင်ရန် အုပ်စု :title ကို ရွေး/မရွေးပါ။',
        ],
        'search' => [
            'label' => 'ရှာဖွေရန်',
            'placeholder' => 'ရှာဖွေရန်',
            'indicator' => 'ရှာဖွေမှု',
        ],
    ],
    'summary' => [
        'heading' => 'အနှစ်ချုပ်',
        'subheadings' => [
            'all' => ':label အားလုံး',
            'group' => ':group အနှစ်ချုပ်',
            'page' => 'ယခုစာမျက်နှာ',
        ],
        'summarizers' => [
            'average' => [
                'label' => 'ပျမ်းမျှ',
            ],
            'count' => [
                'label' => 'အရေအတွက်',
            ],
            'sum' => [
                'label' => 'စုစုပေါင်း',
            ],
        ],
    ],
    'actions' => [
        'disable_reordering' => [
            'label' => 'မှတ်တမ်းများ ပြန်စီခြင်းကို အပြီးသတ်ရန်',
        ],
        'enable_reordering' => [
            'label' => 'မှတ်တမ်းများကို ပြန်စီရန်',
        ],
        'reorder_record' => [
            'label' => 'ပစ္စည်း :key ကို ပြန်စီရန်',
        ],
        'filter' => [
            'label' => 'စစ်ထုတ်ရန်',
        ],
        'group' => [
            'label' => 'အုပ်စုဖွဲ့ရန်',
        ],
        'open_bulk_actions' => [
            'label' => 'အစုလိုက်လုပ်ဆောင်ချက်များ',
        ],
        'column_manager' => [
            'label' => 'ကော်လံ စီမံခန့်ခွဲမှု',
        ],
        'toggle_record_content' => [
            'label' => 'ပစ္စည်း :key ကို ဖြန့်/ခေါက်ရန်',
        ],
    ],
    'empty' => [
        'heading' => ':model မရှိပါ',
        'description' => 'စတင်ရန် :model တစ်ခု ဖန်တီးပါ။',
    ],
    'filters' => [
        'actions' => [
            'apply' => [
                'label' => 'စစ်ထုတ်မှုများကို အသုံးပြုရန်',
            ],
            'remove' => [
                'label' => 'စစ်ထုတ်မှုကို ဖယ်ရှားရန်',
            ],
            'remove_all' => [
                'label' => 'စစ်ထုတ်မှုအားလုံးကို ဖယ်ရှားရန်',
                'tooltip' => 'စစ်ထုတ်မှုအားလုံးကို ဖယ်ရှားရန်',
            ],
            'reset' => [
                'label' => 'မူလအတိုင်း ပြန်ထားရန်',
            ],
        ],
        'heading' => 'စစ်ထုတ်မှုများ',
        'indicator' => 'အသုံးပြုထားသော စစ်ထုတ်မှုများ',
        'multi_select' => [
            'placeholder' => 'အားလုံး',
        ],
        'select' => [
            'placeholder' => 'အားလုံး',
            'relationship' => [
                'empty_option_label' => 'မရှိပါ',
            ],
        ],
        'trashed' => [
            'label' => 'ဖျက်ထားသော မှတ်တမ်းများ',
            'only_trashed' => 'ဖျက်ထားသော မှတ်တမ်းများသာ',
            'with_trashed' => 'ဖျက်ထားသော မှတ်တမ်းများ ပါဝင်သည်',
            'without_trashed' => 'ဖျက်ထားသော မှတ်တမ်းများ မပါဝင်ပါ',
        ],
    ],
    'grouping' => [
        'fields' => [
            'group' => [
                'label' => 'အုပ်စုဖွဲ့မည့်အရာ',
            ],
            'direction' => [
                'label' => 'အုပ်စုဖွဲ့မည့် အစီအစဉ်',
                'options' => [
                    'asc' => 'ငယ်စဉ်ကြီးလိုက်',
                    'desc' => 'ကြီးစဉ်ငယ်လိုက်',
                ],
            ],
        ],
    ],
    'loading' => 'ဖွင့်နေသည်...',
    'reorder_indicator' => 'မှတ်တမ်းများကို အစီအစဉ်တကျဖြစ်စေရန် ဆွဲရွှေ့ပြီး ချပါ။',
    'result_count' => '{0} ရလဒ်မရှိပါ|{1} ရလဒ် :count ခု|[2,*] ရလဒ် :count ခု',
    'selection_indicator' => [
        'selected_count' => 'မှတ်တမ်း ၁ ခု ရွေးထားသည်|မှတ်တမ်း :count ခု ရွေးထားသည်',
        'actions' => [
            'select_all' => [
                'label' => ':count ခုလုံးကို ရွေးရန်',
            ],
            'deselect_all' => [
                'label' => 'အားလုံးကို မရွေးရန်',
            ],
        ],
    ],
    'sorting' => [
        'fields' => [
            'column' => [
                'label' => 'စီမည့်အရာ',
            ],
            'direction' => [
                'label' => 'စီမည့် အစီအစဉ်',
                'options' => [
                    'asc' => 'ငယ်စဉ်ကြီးလိုက်',
                    'desc' => 'ကြီးစဉ်ငယ်လိုက်',
                ],
            ],
        ],
    ],
    'default_model_label' => 'မှတ်တမ်း',
];
