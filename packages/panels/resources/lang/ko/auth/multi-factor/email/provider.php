<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => '이메일 인증 코드',

            'below_content' => '로그인 시 신원 확인을 위해 이메일 주소로 임시 코드를 받습니다.',

            'messages' => [
                'enabled' => '활성화됨',
                'disabled' => '비활성화됨',
            ],

        ],

    ],

    'login_form' => [

        'label' => '이메일로 코드 보내기',

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

];
