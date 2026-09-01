<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => '인증 앱',

            'below_content' => '보안 앱을 사용하여 로그인 확인을 위한 임시 코드를 생성합니다.',

            'messages' => [
                'enabled' => '활성화됨',
                'disabled' => '비활성화됨',
            ],

        ],

    ],

    'login_form' => [

        'label' => '인증 앱의 코드를 사용하세요',

        'code' => [

            'label' => '인증 앱에서 6자리 코드를 입력하세요',

            'validation_attribute' => '코드',

            'actions' => [

                'use_recovery_code' => [
                    'label' => '대신 복구 코드 사용',
                ],

            ],

            'messages' => [

                'invalid' => '입력한 코드가 올바르지 않습니다.',

            ],

        ],

        'recovery_code' => [

            'label' => '또는 복구 코드를 입력하세요',

            'validation_attribute' => '복구 코드',

            'messages' => [

                'invalid' => '입력한 복구 코드가 올바르지 않습니다.',

            ],

        ],

    ],

];
