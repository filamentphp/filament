<?php

return [

    'title' => '회원가입',

    'heading' => '회원가입',

    'actions' => [

        'login' => [
            'before' => '또는',
            'label' => '기존 계정으로 로그인',
        ],

    ],

    'form' => [

        'email' => [
            'label' => '이메일',
        ],

        'name' => [
            'label' => '이름',
        ],

        'password' => [
            'label' => '비밀번호',
            'validation_attribute' => '비밀번호',
        ],

        'password_confirmation' => [
            'label' => '비밀번호 확인',
        ],

        'actions' => [

            'register' => [
                'label' => '회원가입',
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
