<?php

return [

    'label' => '비활성화',

    'modal' => [

        'heading' => '인증 앱 비활성화',

        'description' => '인증 앱 사용을 중단하시겠습니까? 이를 비활성화하면 계정에서 추가 보안 계층이 제거됩니다.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => '인증 앱 비활성화',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => '인증 앱이 비활성화되었습니다',
        ],

    ],

];
