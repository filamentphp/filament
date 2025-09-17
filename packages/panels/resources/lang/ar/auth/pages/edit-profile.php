<?php

return [

    'label' => 'الملف الشخصي',

    'form' => [

        'email' => [
            'label' => 'البريد الإلكتروني',
        ],

        'name' => [
            'label' => 'الاسم',
        ],

        'password' => [
            'label' => 'كلمة المرور الجديدة',
            'validation_attribute' => 'كلمة المرور',
        ],

        'password_confirmation' => [
            'label' => 'تأكيد كلمة المرور الجديدة',
            'validation_attribute' => 'تأكيد كلمة المرور',
        ],

        'current_password' => [
            'label' => 'كلمة المرور الحالية',
            'below_content' => 'لأسباب أمنية، يرجى تأكيد كلمة المرور للمتابعة.',
            'validation_attribute' => 'كلمة المرور الحالية',
        ],

        'actions' => [

            'save' => [
                'label' => 'حفظ التغييرات',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'المصادقة الثنائية (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'تم إرسال طلب لتغيير البريد الإلكتروني',
            'body' => 'تم إرسال طلب لتغيير عنوان بريدك الإلكتروني إلى :email. يرجى التحقق من بريدك الإلكتروني لتأكيد التغيير.',
        ],

        'saved' => [
            'title' => 'تم الحفظ',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'إلغاء',
        ],

    ],

];
