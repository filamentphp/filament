<?php

return [

    'title' => '登錄',

    'heading' => '登錄您的賬號',

    'buttons' => [

        'submit' => [
            'label' => '登錄',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => '郵箱地址',
        ],

        'password' => [
            'label' => '密碼',
        ],

        'remember' => [
            'label' => '記住我',
        ],

    ],

    'messages' => [
        'failed' => '登錄憑證與記錄不符。',
        'throttled' => '嘗試登錄次數過多，請在 :seconds 秒後重試。',
    ],

];
