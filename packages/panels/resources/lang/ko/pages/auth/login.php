<?php

return [

    'title' => '로그인',

    'heading' => '로그인하세요',

    'form' => [

        'email' => [
            'label' => '이메일',
        ],

        'password' => [
            'label' => '비밀번호',
        ],

        'remember' => [
            'label' => '기억하기',
        ],

        'actions' => [

            'authenticate' => [
                'label' => '로그인',
            ],

        ],

    ],

    'messages' => [

        'failed' => '일치하는 계정이 없습니다.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => '로그인 시도가 너무 많이 발생했습니다. :seconds 초 후에 다시 시도 해주세요.',
        ],

    ],

];
