<?php

return [

    'label' => 'פרופיל',

    'form' => [

        'email' => [
            'label' => 'כתובת דוא"ל',
        ],

        'name' => [
            'label' => 'שם',
        ],

        'password' => [
            'label' => 'סיסמה חדשה',
            'validation_attribute' => 'סיסמה',
        ],

        'password_confirmation' => [
            'label' => 'אימות סיסמה חדשה',
            'validation_attribute' => 'אימות סיסמה',
        ],

        'current_password' => [
            'label' => 'סיסמה נוכחית',
            'below_content' => 'מטעמי אבטחה, אשר את הסיסמה שלך כדי להמשיך.',
            'validation_attribute' => 'סיסמה נוכחית',
        ],

        'actions' => [

            'save' => [
                'label' => 'שמור שינויים',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'אימות דו-שלבי (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'נשלחה בקשה לשינוי כתובת הדוא"ל',
            'body' => 'בקשה לשינוי כתובת הדוא"ל שלך נשלחה אל :email. בדוק את הדוא"ל שלך כדי לאמת את השינוי.',
        ],

        'saved' => [
            'title' => 'נשמר',
        ],

        'throttled' => [
            'title' => 'יותר מדי בקשות. נסה שוב בעוד :seconds שניות.',
            'body' => 'אנא נסה שוב בעוד :seconds שניות.',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'ביטול',
        ],

    ],

];
