<?php

return [

    'label' => 'Thiết lập',

    'modal' => [

        'heading' => 'Thiết lập ứng dụng xác thực',

        'description' => <<<'BLADE'
            Bạn cần một ứng dụng như Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) để hoàn thành quá trình này.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Quét mã QR này với ứng dụng xác thực của bạn:',

                'alt' => 'Mã QR để quét với ứng dụng xác thực',

            ],

            'text_code' => [

                'instruction' => 'Hoặc nhập mã này thủ công:',

                'messages' => [
                    'copied' => 'Đã sao chép',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Vui lòng lưu các mã khôi phục này ở một nơi an toàn. Chúng sẽ chỉ được hiển thị một lần, nhưng bạn sẽ cần chúng nếu bạn mất quyền truy cập vào ứng dụng xác thực của mình:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Nhập mã 6 chữ số từ ứng dụng xác thực',

                'validation_attribute' => 'mã xác thực',

                'below_content' => 'Bạn sẽ cần nhập mã 6 chữ số từ ứng dụng xác thực mỗi khi đăng nhập hoặc thực hiện hành động nhạy cảm.',

                'messages' => [

                    'invalid' => 'Mã bạn đã nhập không hợp lệ.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Kích hoạt ứng dụng xác thực',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Ứng dụng xác thực đã được kích hoạt',
        ],

    ],

];
