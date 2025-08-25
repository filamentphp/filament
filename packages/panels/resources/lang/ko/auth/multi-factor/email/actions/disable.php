<?php

return [

    'label' => '비활성화',

    'modal' => [

        'heading' => '이메일 인증 코드 비활성화',

        'description' => '이메일 인증 코드 수신을 중단하시겠습니까? 이를 비활성화하면 계정에서 추가 보안 계층이 제거됩니다.',

        'form' => [

            'code' => [

                'label' => '이메일로 보낸 6자리 코드를 입력하세요',

                'validation_attribute' => '코드',

                'actions' => [

                    'resend' => [

                        'label' => '이메일로 새 코드 보내기',

                        'notifications' => [

                            'resent' => [
                                'title' => '이메일로 새 코드를 보냈습니다',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => '입력한 코드가 올바르지 않습니다.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '이메일 인증 코드 비활성화',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => '이메일 인증 코드가 비활성화되었습니다',
        ],

    ],

];
