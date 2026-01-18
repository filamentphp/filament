<?php

return [

    'label' => 'Iskluči',

    'modal' => [

        'heading' => 'Onevozmoži kodovi za verifikacija po e-pošta',

        'description' => 'Dali ste sigurni deka sakate da prestanete da primаte kodovi za verifikacija po e-pošta? Onevozmožuvanjeto na ova ќe go otstаni dopolnitelniot sloj na bezbednost od vašata smetka.',

        'form' => [

            'code' => [

                'label' => 'Vnesete go 6-cifreniot kod što vi go isprатиvme po e-pošta',

                'validation_attribute' => 'kod',

                'actions' => [

                    'resend' => [

                        'label' => 'Ispraти nov kod po e-pošta',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Vi isprатиvme nov kod po e-pošta',
                            ],

                            'throttled' => [
                                'title' => 'Premnogu obidi za povtorno isprаќanje. Ve molime počekајte pred da pobаrаte drug kod.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Kodot što go vnesovte ne e validen.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Onevozmoži kodovi za verifikacija po e-pošta',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Kodovite za verifikacija po e-pošta se onevozmoženi',
        ],

    ],

];
