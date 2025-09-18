<?php

return [

    'label' => '설정',

    'modal' => [

        'heading' => '이메일 인증 코드 설정',

        'description' => '로그인하거나 민감한 작업을 수행할 때마다 이메일로 보낸 6자리 코드를 입력해야 합니다. 설정을 완료하려면 이메일에서 6자리 코드를 확인하세요.',

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
                'label' => '이메일 인증 코드 활성화',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => '이메일 인증 코드가 활성화되었습니다',
        ],

    ],

];
