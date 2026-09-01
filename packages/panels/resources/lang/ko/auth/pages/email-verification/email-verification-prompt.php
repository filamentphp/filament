<?php

return [

    'title' => '이메일 주소 확인',

    'heading' => '이메일 주소 확인',

    'actions' => [

        'resend_notification' => [
            'label' => '재전송',
        ],

    ],

    'messages' => [
        'notification_not_received' => '이메일을 받지 못하셨나요?',
        'notification_sent' => '이메일 인증에 대한 안내가 포함된 이메일을 :email로 보냈습니다.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => '이메일을 다시 보냈습니다.',
        ],

        'notification_resend_throttled' => [
            'title' => '너무 많이 시도했습니다',
            'body' => ':seconds 초 후에 다시 시도해 주세요.',
        ],

    ],

];
