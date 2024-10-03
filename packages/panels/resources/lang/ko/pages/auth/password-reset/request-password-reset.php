<?php

return [

    'title' => '비밀번호 재설정',

    'heading' => '비밀번호를 잊어버리셨나요?',

    'actions' => [

        'login' => [
            'label' => '로그인으로 돌아가기',
        ],

    ],

    'form' => [

        'email' => [
            'label' => '이메일',
        ],

        'actions' => [

            'request' => [
                'label' => '이메일 전송',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => '너무 많이 시도했습니다',
            'body' => ':seconds 초 후에 다시 시도해 주세요.',
        ],

    ],

];
