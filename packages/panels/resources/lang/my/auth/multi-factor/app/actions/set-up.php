<?php

return [
    'label' => 'စီစဉ်သတ်မှတ်ရန်',
    'modal' => [
        'heading' => 'အထောက်အထားစစ်ဆေးသည့် အက်ပ်ကို စီစဉ်သတ်မှတ်ရန်',
        'description' => 'ဤလုပ်ငန်းစဉ်ကို ပြီးမြောက်ရန် Google Authenticator ကဲ့သို့သော အက်ပ်တစ်ခု (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>၊ <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) လိုအပ်ပါမည်။',
        'content' => [
            'qr_code' => [
                'instruction' => 'ဤ QR ကုဒ်ကို အထောက်အထားစစ်ဆေးသည့် အက်ပ်ဖြင့် စကင်ဖတ်ပါ။',
                'alt' => 'အထောက်အထားစစ်ဆေးသည့် အက်ပ်ဖြင့် စကင်ဖတ်ရန် QR ကုဒ်',
            ],
            'text_code' => [
                'instruction' => 'သို့မဟုတ် ဤကုဒ်ကို ကိုယ်တိုင် ထည့်ပါ။',
                'messages' => [
                    'copied' => 'ကူးယူပြီးပါပြီ',
                ],
            ],
            'recovery_codes' => [
                'instruction' => 'အောက်ပါ ပြန်လည်ရယူရေးကုဒ်များကို လုံခြုံသည့်နေရာတွင် သိမ်းထားပါ။ ၎င်းတို့ကို တစ်ကြိမ်သာ ပြသမည်ဖြစ်ပြီး အထောက်အထားစစ်ဆေးသည့် အက်ပ်ကို အသုံးမပြုနိုင်တော့ပါက လိုအပ်ပါမည်။',
            ],
        ],
        'form' => [
            'code' => [
                'label' => 'အထောက်အထားစစ်ဆေးသည့် အက်ပ်မှ ဂဏန်း ၆ လုံးပါ ကုဒ်ကို ထည့်ပါ',
                'validation_attribute' => 'ကုဒ်',
                'below_content' => 'အကောင့်ဝင်သည့်အခါတိုင်း သို့မဟုတ် အရေးကြီးသည့် လုပ်ဆောင်ချက်များ ပြုလုပ်သည့်အခါတိုင်း အထောက်အထားစစ်ဆေးသည့် အက်ပ်မှ ဂဏန်း ၆ လုံးပါ ကုဒ်ကို ထည့်ရန် လိုအပ်ပါမည်။',
                'messages' => [
                    'invalid' => 'သင်ထည့်သွင်းသော ကုဒ် မမှန်ကန်ပါ။',
                    'rate_limited' => 'ကြိုးစားမှု များလွန်းနေပါသည်။ နောက်မှ ထပ်စမ်းကြည့်ပါ။',
                ],
            ],
        ],
        'actions' => [
            'submit' => [
                'label' => 'အထောက်အထားစစ်ဆေးသည့် အက်ပ်ကို ဖွင့်ရန်',
            ],
        ],
    ],
    'notifications' => [
        'enabled' => [
            'title' => 'အထောက်အထားစစ်ဆေးသည့် အက်ပ်ကို ဖွင့်ပြီးပါပြီ',
        ],
    ],
];
