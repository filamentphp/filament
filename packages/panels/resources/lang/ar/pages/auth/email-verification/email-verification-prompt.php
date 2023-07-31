<?php

return [

    'title' => 'تأكيد بريدك الإلكتروني',

    'heading' => 'تأكيد بريدك الإلكتروني',

    'actions' => [

        'resend_notification' => [
            'label' => 'إعادة الإرسال',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'لم تستلم البريد الذي قمنا بإرساله؟',
        'notification_sent' => 'قمنا بإرسال بريد إلكتروني إلى :email يحتوي على تعليمات حول طريقة تفعيل بريدك الإلكتروني.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'لقد قمنا بإعادة إرسال البريد الإلكتروني.',
        ],

        'notification_resend_throttled' => [
            'title' => 'لقد قمت بمحاولات إعادة إرسال كثيرة جداً',
            'body' => 'يرجى المحاولة مرة أخرى بعد :seconds ثواني.',
        ],

    ],

];
