<?php

return [

    'label' => '프로필',

    'form' => [

        'email' => [
            'label' => '이메일',
        ],

        'name' => [
            'label' => '이름',
        ],

        'password' => [
            'label' => '새 비밀번호',
            'validation_attribute' => '비밀번호',
        ],

        'password_confirmation' => [
            'label' => '새 비밀번호 확인',
            'validation_attribute' => '비밀번호 확인',
        ],

        'current_password' => [
            'label' => '현재 비밀번호',
            'below_content' => '보안을 위해 계속하려면 비밀번호를 확인해 주세요.',
            'validation_attribute' => '현재 비밀번호',
        ],

        'actions' => [

            'save' => [
                'label' => '변경 사항 저장',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => '이중 인증 (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => '이메일 주소 변경 요청이 전송됨',
            'body' => ':email로 이메일 주소 변경 요청이 전송되었습니다. 변경사항을 확인하려면 이메일을 확인해 주세요.',
        ],

        'saved' => [
            'title' => '저장 완료',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => '취소',
        ],

    ],

];
