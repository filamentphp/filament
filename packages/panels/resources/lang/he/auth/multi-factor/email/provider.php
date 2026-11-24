<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'קודי אימות בדוא"ל',

            'below_content' => 'קבל קוד זמני לכתובת הדוא"ל שלך כדי לאמת את זהותך בעת ההתחברות.',

            'messages' => [
                'enabled' => 'מופעל',
                'disabled' => 'מושבת',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'שלח קוד לדוא"ל שלך',

        'code' => [

            'label' => 'הזן את הקוד בן 6 הספרות ששלחנו אליך בדוא"ל',

            'validation_attribute' => 'קוד',

            'actions' => [

                'resend' => [

                    'label' => 'שלח קוד חדש בדוא"ל',

                    'notifications' => [

                        'resent' => [
                            'title' => 'שלחנו אליך קוד חדש בדוא"ל',
                        ],

                        'throttled' => [
                            'title' => 'יותר מדי ניסיונות שליחה חוזרת. המתן לפני שתבקש קוד נוסף.',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'הקוד שהזנת אינו תקין.',

            ],

        ],

    ],

];
