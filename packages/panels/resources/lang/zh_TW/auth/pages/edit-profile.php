<?php

return [

    'label' => '個人資料',

    'form' => [

        'email' => [
            'label' => '電子郵件地址',
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
            'label' => '目前密碼',
            'below_content' => '為了安全起見，請確認您的密碼以繼續。',
            'validation_attribute' => '目前密碼',
        ],

        'actions' => [

            'save' => [
                'label' => '儲存變更',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => '雙因素驗證 (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => '電子郵件地址變更請求已發送',
            'body' => '變更電子郵件地址的請求已發送至 :email。請檢查您的電子郵件以驗證變更。',
        ],

        'saved' => [
            'title' => '已儲存',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => '取消',
        ],

    ],

];
