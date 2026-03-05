<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Email verification codes',

            'below_content' => 'Receive a temporary code at your email address to verify your identity during login.',

            'messages' => [
                'enabled' => 'Enabled',
                'disabled' => 'Disabled',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Send a code to your email',

        'code' => [

            'label' => 'Enter the 6-digit code we sent you by email',

            'validation_attribute' => 'code',

            'actions' => [

                'resend' => [

                    'label' => 'Send a new code by email',

                    'notifications' => [

                        'resent' => [
                            'title' => 'We\'ve sent you a new code by email',
                        ],

                        'throttled' => [
                            'title' => 'Too many resend attempts. Please wait before requesting another code.',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'The code you entered is invalid.',

            ],

        ],

    ],

];
