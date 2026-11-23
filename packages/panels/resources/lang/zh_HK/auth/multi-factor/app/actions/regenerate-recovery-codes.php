<?php

return [

    'label' => '重新產生復原碼',

    'modal' => [

        'heading' => '重新產生驗證器應用程式復原碼',

        'description' => '如遺失復原碼，你可以在此產生新的復原碼。舊有的復原碼會立即失效。',

        'form' => [

            'code' => [

                'label' => '輸入驗證器應用程式顯示的 6 位數字驗證碼',

                'validation_attribute' => '驗證碼',

                'messages' => [

                    'invalid' => '你輸入的驗證碼無效。',

                    'rate_limited' => '嘗試次數過多，請稍後再試。',

                ],

            ],

            'password' => [

                'label' => '輸入現有密碼',

                'validation_attribute' => '密碼',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '重新產生復原碼',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => '已產生新的驗證器應用程式復原碼',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => '新復原碼',

            'description' => '請將以下復原碼妥善保存在安全的地方。復原碼只會顯示一次，如無法使用驗證器應用程式，你便需要使用這些復原碼：',

            'actions' => [

                'submit' => [
                    'label' => '關閉',
                ],

            ],

        ],

    ],

];
