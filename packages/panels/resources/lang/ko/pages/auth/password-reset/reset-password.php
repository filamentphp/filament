<?php

return [

    'title' => '비밀번호 재설정',

    'heading' => '비밀번호 재설정',

    'form' => [

        'email' => [
            'label' => '이메일',
        ],

        'password' => [
            'label' => '비밀번호',
            'validation_attribute' => '비밀번호',
        ],

        'password_confirmation' => [
            'label' => '비밀번호 확인',
        ],

        'actions' => [

            'reset' => [
                'label' => '비밀번호 재설정',
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
