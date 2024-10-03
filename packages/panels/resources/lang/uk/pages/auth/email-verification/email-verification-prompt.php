<?php

return [

    'title' => 'Підтвердіть електронну пошту',

    'heading' => 'Підтвердіть електронну пошту',

    'actions' => [

        'resend_notification' => [
            'label' => 'Надіслати ще раз',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Не отримали нашого листа?',
        'notification_sent' => 'Ми надіслали на пошту :email лист з поясненням, як підтвердити свою електронну пошту.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Ми надіслали вам лист ще раз.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Забагато спроб повторно надіслати лист',
            'body' => 'Будь ласка, спробуйте ще раз через :seconds секунд.',
        ],

    ],

];
