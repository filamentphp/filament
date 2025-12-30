<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => '驗證器應用程式',

            'below_content' => '使用安全應用程式生成臨時驗證碼以進行登入驗證。',

            'messages' => [
                'enabled' => '已啟用',
                'disabled' => '已停用',
            ],

        ],

    ],

    'login_form' => [

        'label' => '使用驗證器應用程式中的驗證碼',

        'code' => [

            'label' => '輸入來自驗證器應用程式的 6 位數驗證碼',

            'validation_attribute' => '驗證碼',

            'actions' => [

                'use_recovery_code' => [
                    'label' => '改用復原碼',
                ],

            ],

            'messages' => [

                'invalid' => '您輸入的驗證碼無效。',

            ],

        ],

        'recovery_code' => [

            'label' => '或，輸入復原碼',

            'validation_attribute' => '復原碼',

            'messages' => [

                'invalid' => '您輸入的復原碼無效。',

            ],

        ],

    ],

];
