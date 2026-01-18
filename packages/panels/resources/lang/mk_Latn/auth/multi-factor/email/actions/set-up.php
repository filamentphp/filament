<?php

return [

    'label' => 'Postavi',

    'modal' => [

        'heading' => 'Postavi kodovi za verifikacija po e-pošta',

        'description' => 'Ќe treba da go vnesete 6-cifreniot kod što vi go isprаќаme po e-pošta sekoj pat koga ќe se najavite ili izvršite čuvstvitelni akcii. Proverete ja vašata e-pošta za 6-cifren kod za da go završite postаvuvanjeto.',

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
                'label' => 'Ovozmoži kodovi za verifikacija po e-pošta',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Kodovite za verifikacija po e-pošta se ovozmoženi',
        ],

    ],

];
