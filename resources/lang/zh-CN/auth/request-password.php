<?php

return [

    'buttons' => [

        'submit' => [
            'label' => '发送密码重置链接',
        ],

    ],

    'form' => [

        'email' => [
            'hint' => '回到登录',
            'label' => '邮箱地址',
        ],

    ],

    'messages' => [

        'throttled' => '错误次数过多. 请在 :seconds 秒后重试.',

        'passwords' => [
            'sent' => '我们已经向您的邮箱发送密码重置链接!',
            'throttled' => '请稍后重试.',
            'user' => '找不到您填写的邮箱.',
        ],

    ],

    'title' => '重置密码',

];
