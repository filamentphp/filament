<?php

return [

    'title' => 'Потвърдете вашия имейл адрес',

    'heading' => 'Потвърдете вашия имейл адрес',

    'actions' => [

        'resend_notification' => [
            'label' => 'Изпратете ми отново имейл за потвърждение',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Ако не сте получили имейла',
        'notification_sent' => 'Имейлът за потвърждение беше изпратен на вашия имейл адрес.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Имейлът за потвърждение беше изпратен',
        ],

        'notification_resend_throttled' => [
            'title' => 'Твърде много опити за изпращане на имейл за потвърждение',
            'body' => 'Моля, опитайте отново след :seconds секунди.',
        ],

    ],

];
