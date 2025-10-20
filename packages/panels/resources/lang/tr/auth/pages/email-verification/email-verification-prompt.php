<?php

return [

    'title' => 'E-posta adresinizi doğrulayın',

    'heading' => 'E-posta adresinizi doğrulayın',

    'actions' => [

        'resend_notification' => [
            'label' => 'Yeniden Gönder',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Gönderdiğimiz e-postayı almadınız mı?',
        'notification_sent' => ':email adresine, e-posta adresinizi nasıl doğrulayacağınıza ilişkin talimatları içeren bir e-posta gönderdik.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'E-posta yeniden gönderildi.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Çok fazla yeniden gönderme denemesi',
            'body' => 'Lütfen :seconds saniye sonra tekrar deneyin.',
        ],

    ],

];
