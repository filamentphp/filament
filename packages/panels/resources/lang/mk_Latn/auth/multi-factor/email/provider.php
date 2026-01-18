<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Kodovi za verifikacija po e-pošta',

            'below_content' => 'Primајte privremen kod na vašata e-pošta adresa za da go potvrdite vašiot identitet za vreme na najavuvanjeto.',

            'messages' => [
                'enabled' => 'Ovozmoženo',
                'disabled' => 'Onevozmoženo',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Ispraти kod na vašata e-pošta',

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

];
