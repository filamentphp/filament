<?php

return [

    'title' => 'تحقق من عنوان بريدك الإلكتروني',

    'heading' => 'تحقق من عنوان بريدك الإلكتروني',

    'actions' => [

        'resend_notification' => [
            'label' => 'أعد الإرسال',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'لم تستلم البريد الذي قمنا بإرساله؟',
        'notification_sent' => 'لقد أرسلنا بريدًا إلكترونيًا إلى :email يحتوي على تعليمات حول كيفية التحقق من عنوان بريدك الإلكتروني.',
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
