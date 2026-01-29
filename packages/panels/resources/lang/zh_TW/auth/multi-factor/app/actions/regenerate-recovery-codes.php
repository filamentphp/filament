<?php

return [

    'label' => '重新生成復原碼',

    'modal' => [

        'heading' => '重新生成驗證器應用程式復原碼',

        'description' => '如果您遺失了復原碼，可以在這裡重新生成它們。您的舊復原碼將立即失效。',

        'form' => [

            'code' => [

                'label' => '輸入來自驗證器應用程式的 6 位數驗證碼',

                'validation_attribute' => '驗證碼',

                'messages' => [

                    'invalid' => '您輸入的驗證碼無效。',

                ],

            ],

            'password' => [

                'label' => '或輸入您目前的密碼',

                'validation_attribute' => '密碼',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '重新生成復原碼',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => '已生成新的驗證器應用程式復原碼',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => '新復原碼',

            'description' => '請將以下復原碼保存在安全的地方。它們只會顯示一次，但如果您無法存取驗證器應用程式，您將需要它們：',

            'actions' => [

                'submit' => [
                    'label' => '關閉',
                ],

            ],

        ],

    ],

];
