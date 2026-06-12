<?php

return [

    'title' => '로그인',

    'heading' => '로그인하세요',

    'actions' => [

        'register' => [
            'before' => '또는',
            'label' => '회원 가입',
        ],

        'request_password_reset' => [
            'label' => '비밀번호를 잊어버리셨나요?',
        ],

    ],

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

    'multi_factor' => [

        'heading' => '신원 확인',

        'subheading' => '로그인을 계속하려면 신원을 확인해야 합니다.',

        'form' => [

            'provider' => [
                'label' => '어떻게 확인하시겠습니까?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => '로그인 확인',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => '일치하는 계정이 없습니다.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => '너무 많이 시도했습니다',
            'body' => ':seconds 초 후에 다시 시도해 주세요.',
        ],

    ],

];
