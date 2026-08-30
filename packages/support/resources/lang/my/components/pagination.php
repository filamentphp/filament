<?php

return [
    'label' => 'စာမျက်နှာခွဲ လမ်းညွှန်',
    'overview' => '{1} ရလဒ် ၁ ခုကို ပြသထားသည်|[2,*] ရလဒ် :total ခုအနက် :first မှ :last အထိ ပြသထားသည်',
    'fields' => [
        'records_per_page' => [
            'label' => 'တစ်မျက်နှာလျှင်',
            'options' => [
                'all' => 'အားလုံး',
            ],
        ],
    ],
    'actions' => [
        'first' => [
            'label' => 'ပထမ',
        ],
        'go_to_page' => [
            'label' => 'စာမျက်နှာ :page သို့ သွားရန်',
        ],
        'last' => [
            'label' => 'နောက်ဆုံး',
        ],
        'next' => [
            'label' => 'ရှေ့သို့',
        ],
        'previous' => [
            'label' => 'နောက်သို့',
        ],
    ],
];
