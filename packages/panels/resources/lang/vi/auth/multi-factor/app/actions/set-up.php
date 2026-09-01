<?php

return [

    'label' => 'Cài đặt',

    'modal' => [

        'heading' => 'Cài đặt ứng dụng xác thực',

        'description' => <<<'BLADE'
            Bạn sẽ cần một ứng dụng như Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) để hoàn tất quá trình này.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Quét mã QR này bằng ứng dụng xác thực của bạn:',

                'alt' => 'Mã QR để quét bằng ứng dụng xác thực',

            ],

            'text_code' => [

                'instruction' => 'Hoặc nhập mã này thủ công:',

                'messages' => [
                    'copied' => 'Đã sao chép',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Vui lòng lưu các mã khôi phục sau ở nơi an toàn. Bạn chỉ xem được một lần duy nhất là lúc này, và sẽ cần dùng chúng nếu không mở được ứng dụng xác thực:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Nhập mã 6 số từ ứng dụng xác thực',

                'validation_attribute' => 'mã',

                'below_content' => 'Bạn sẽ cần nhập mã 6 số từ ứng dụng xác thực mỗi khi đăng nhập hoặc thực hiện các thao tác nhạy cảm.',

                'messages' => [

                    'invalid' => 'Mã bạn vừa nhập không hợp lệ.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Bật ứng dụng xác thực',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Đã bật ứng dụng xác thực',
        ],

    ],

];
