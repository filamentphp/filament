<?php

return [

    'label' => '복구 코드 재생성',

    'modal' => [

        'heading' => '인증 앱 복구 코드 재생성',

        'description' => '복구 코드를 분실한 경우 여기에서 재생성할 수 있습니다. 기존 복구 코드는 즉시 무효화됩니다.',

        'form' => [

            'code' => [

                'label' => '인증 앱에서 6자리 코드를 입력하세요',

                'validation_attribute' => '코드',

                'messages' => [

                    'invalid' => '입력한 코드가 올바르지 않습니다.',

                ],

            ],

            'password' => [

                'label' => '또는 현재 비밀번호를 입력하세요',

                'validation_attribute' => '비밀번호',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '복구 코드 재생성',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => '새로운 인증 앱 복구 코드가 생성되었습니다',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => '새 복구 코드',

            'description' => '다음 복구 코드를 안전한 곳에 저장하세요. 한 번만 표시되며, 인증 앱에 액세스할 수 없을 때 필요합니다:',

            'actions' => [

                'submit' => [
                    'label' => '닫기',
                ],

            ],

        ],

    ],

];
