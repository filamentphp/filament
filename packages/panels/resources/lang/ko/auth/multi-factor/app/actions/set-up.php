<?php

return [

    'label' => '설정',

    'modal' => [

        'heading' => '인증 앱 설정',

        'description' => <<<'BLADE'
            이 과정을 완료하려면 Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>)와 같은 앱이 필요합니다.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => '인증 앱으로 이 QR 코드를 스캔하세요:',

                'alt' => '인증 앱으로 스캔할 QR 코드',

            ],

            'text_code' => [

                'instruction' => '또는 이 코드를 수동으로 입력하세요:',

                'messages' => [
                    'copied' => '복사됨',
                ],

            ],

            'recovery_codes' => [

                'instruction' => '다음 복구 코드를 안전한 곳에 저장하세요. 한 번만 표시되며, 인증 앱에 액세스할 수 없을 때 필요합니다:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => '인증 앱에서 6자리 코드를 입력하세요',

                'validation_attribute' => '코드',

                'below_content' => '로그인하거나 민감한 작업을 수행할 때마다 인증 앱에서 6자리 코드를 입력해야 합니다.',

                'messages' => [

                    'invalid' => '입력한 코드가 올바르지 않습니다.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '인증 앱 활성화',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => '인증 앱이 활성화되었습니다',
        ],

    ],

];
