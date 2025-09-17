<?php

return [

    'label' => 'Set up',

    'modal' => [

        'heading' => 'Set up email verification codes',

        'description' => 'You\'ll need to enter the 6-digit code we send you by email each time you sign in or perform sensitive actions. Check your email for a 6-digit code to complete the setup.',

        'form' => [

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

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'The code you entered is invalid.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Enable email verification codes',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Email verification codes have been enabled',
        ],

    ],

];
