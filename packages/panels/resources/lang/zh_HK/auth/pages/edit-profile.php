<?php

return [

    'label' => '個人資料',

    'form' => [

        'email' => [
            'label' => '電郵地址',
        ],

        'name' => [
            'label' => '姓名',
        ],

        'password' => [
            'label' => '新密碼',
            'validation_attribute' => '密碼',
        ],

        'password_confirmation' => [
            'label' => '確認新密碼',
            'validation_attribute' => '密碼確認',
        ],

        'current_password' => [
            'label' => '現有密碼',
            'below_content' => '為保障帳戶安全，請輸入現有密碼以繼續。',
            'validation_attribute' => '現有密碼',
        ],

        'actions' => [

            'save' => [
                'label' => '儲存變更',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => '雙重認證 (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => '更改電郵地址的要求已發出',
            'body' => '確認更改電郵地址的電郵已傳送至 :email。請查閱電郵並確認更改。',
        ],

        'saved' => [
            'title' => '已儲存',
        ],

        'throttled' => [
            'title' => '要求次數過多，請在 :seconds 秒後再試一次。',
            'body' => '請在 :seconds 秒後再試一次。',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => '取消',
        ],

    ],

];
